<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

use Liangguifeng\LotteryAnalyzer\Enum\ErrorCode;
use Liangguifeng\LotteryAnalyzer\Exceptions\InvalidDataException;
use Liangguifeng\LotteryAnalyzer\Support\ArrayHelper;

abstract class AbstractAnalyzer implements AnalyzerInterface
{
    /**
     * @var array 历史开奖数据
     */
    protected array $historyData;

    /**
     * @var bool 是否返回最大连续期数
     */
    protected bool $withMaxConsecutive = false;

    /**
     * Constructor.
     *
     * @param array $historyData 历史开奖数据
     */
    public function __construct(array $historyData)
    {
        if (empty($historyData)) {
            throw new InvalidDataException('History lottery data can not be empty.', ErrorCode::EMPTY_HISTORY);
        }

        // 先排序，防呆...
        krsort($historyData);
        $this->historyData = $historyData;
    }

    /**
     * 是否返回最大连续期数.
     *
     * @param bool $withMaxConsecutive
     * @return static
     */
    public function withMaxConsecutive(bool $withMaxConsecutive): static
    {
        $this->withMaxConsecutive = $withMaxConsecutive;

        return $this;
    }

    /**
     * 获取待分析数据(剔除预测数据的结果集).
     *
     * @param int $analyzePeriods 分析期数
     * @return array
     */
    protected function getAnalyzerData(int $analyzePeriods): array
    {
        return array_slice($this->historyData, $analyzePeriods, null, true);
    }

    /**
     * 解析路径坐标.
     *
     * @param string $path
     * @return array
     */
    protected function parsePathCoords(string $path): array
    {
        $coords = [];

        foreach (explode('|', $path) as $p) {
            if (strpos($p, '_') === false) {
                continue;
            }

            [$g, $pos] = explode('_', $p);
            $g = (int) $g;
            $pos = (int) $pos;

            if ($g <= 0 || $pos <= 0) {
                continue;
            }

            $coords[$g][] = $pos;
        }

        foreach ($coords as $g => $arr) {
            $coords[$g] = array_values(array_unique($arr));
        }

        return $coords;
    }

    /**
     * 获取结果集的交集.
     *
     * @param array $hitLists 所有分块命中的结果集
     * @return array
     */
    protected function intersectHitResults(array $hitLists): array
    {
        if (empty($hitLists)) {
            return [];
        }

        $result = reset($hitLists);
        foreach ($hitLists as $hitList) {
            $result = array_intersect_key($result, $hitList);
        }

        return array_keys($result);
    }

    /**
     * 根据命中路径，处理历史数据.
     *
     * @param array $history 历史数据
     * @param int $analyzePeriods 分析期数
     * @param int $minConsecutive 最小连续命中期数
     * @param int $intervalPeriods 间隔期数
     * @param string $path 命中的具体坐标
     * @return array
     */
    protected function processHistory(array $history, int $analyzePeriods, int $minConsecutive, int $intervalPeriods, string $path): array
    {
        // 1. 按 key 升序排序
        ksort($history);
        $keys = array_keys($history);
        $total = count($keys);
        if ($total === 0) {
            return [];
        }

        // 2. 从最大 key 开始分片
        $groups = [];
        $cursor = $total;

        // 第一个分片：取 分析期数 个
        $take = min($analyzePeriods, $cursor);
        $start = $cursor - $take;
        $groups[] = array_slice($keys, $start, $take); // 保持升序
        $cursor -= $take;

        // 后续每轮取 分析期数 + 间隔期数 + 预测期数 个
        while ($cursor > 0) {
            $take = min($analyzePeriods + $intervalPeriods + 1, $cursor);
            $start = $cursor - $take;
            $groups[] = array_slice($keys, $start, $take);
            $cursor -= $take;
        }

        // 3. 解析 path => [ group => [pos, pos] ]
        $coords = $this->parsePathCoords($path);

        // 4. 处理每个分片
        $result = [];
        foreach ($groups as $times => $groupKeys) {
            // groupKeys 是分片内升序排列的期号数组
            foreach ($groupKeys as $localIndex => $periodKey) {
                $groupNum = $localIndex + 1; // 分片内 group 编号从1开始
                $origin = $history[$periodKey];
                $hit = [];

                if (isset($coords[$groupNum])) {
                    foreach ($coords[$groupNum] as $pos) {
                        if (isset($origin[$pos - 1])) {
                            $hit[] = $pos;
                        }
                    }
                }

                $result[$periodKey] = [
                    'origin' => $origin,
                    'hit' => [],
                    'periods' => $periodKey,
                    'is_predict' => false,
                ];

                if ($times < $minConsecutive) {
                    $result[$periodKey]['hit'] = array_values($hit);
                    // 预测位标记
                    if ($localIndex === $analyzePeriods + $intervalPeriods) {
                        $result[$periodKey]['is_predict'] = true;
                    } else {
                        $result[$periodKey]['is_predict'] = false;
                    }
                }
            }
        }

        // 5. 最终按 key 升序返回
        ksort($result);

        return $result;
    }

    /**
     * 格式化结果集.
     *
     * @param array $paths 命中的全部坐标
     * @param int $analyzePeriods 分析期数
     * @param int $minConsecutive 最小连续命中期数
     * @param int $intervalPeriods 间隔期数
     * @return array
     */
    protected function formatResult(array $paths, int $analyzePeriods, int $minConsecutive, int $intervalPeriods): array
    {
        $result = [];
        foreach ($paths as $path) {
            // 是否计算最大连续命中期数
            $maxConsecutive = 0;
            if ($this->withMaxConsecutive) {
                $maxConsecutive = $this->getMaxConsecutive($path, $analyzePeriods);
            }

            $hitList = $this->processHistory($this->historyData, $analyzePeriods, $minConsecutive, $intervalPeriods, $path);

            $result[] = [
                'path_string' => $path,
                'path' => explode('|', $path),
                'max_consecutive' => $maxConsecutive,
                'items' => $hitList,
            ];
        }

        usort($result, fn($a, $b) => $b['max_consecutive'] <=> $a['max_consecutive']);

        return $result;
    }

    /**
     * 获取最大连续命中期数.
     *
     * @param string $path
     * @param int $analyzePeriods
     * @return int
     */
    protected function getMaxConsecutive(string $path, int $analyzePeriods): int
    {
        // 分析数据(剔除预测数据的结果集)
        $analyzerData = $this->getAnalyzerData($analyzePeriods);

        // 拆分为规律区间
        $chunks = ArrayHelper::chuck($analyzerData, $analyzePeriods + 1);

        // 解析并格式化坐标
        $coords = $this->parsePathCoords($path);

        $maxConsecutive = 0;

        foreach ($chunks as $chunk) {
            if (!$this->isChunkMatch($chunk, $coords, $analyzePeriods)) {
                break;
            }

            ++$maxConsecutive;
        }

        return $maxConsecutive;
    }

    /**
     * 检查块是否匹配.
     *
     * @param array $chunk
     * @param array $coords
     * @param int $analyzePeriods
     * @return bool
     */
    protected function isChunkMatch(array $chunk, array $coords, int $analyzePeriods): bool
    {
        ksort($chunk);
        $chunkValues = array_values($chunk);

        $waitCheckList = array_slice($chunkValues, 0, $analyzePeriods);
        $checkTarget = $chunkValues[$analyzePeriods] ?? [];

        if (empty($checkTarget) || empty($waitCheckList)) {
            return false;
        }

        $waitCheckValues = [];
        foreach ($coords as $groupIndex => $positions) {
            if (!isset($waitCheckList[$groupIndex - 1])) {
                continue;
            }

            $groupData = $waitCheckList[$groupIndex - 1];
            foreach ($positions as $position) {
                if (isset($groupData[$position - 1])) {
                    $waitCheckValues[] = $groupData[$position - 1];
                }
            }
        }

        return $this->checkMatch($waitCheckValues, $checkTarget);
    }

    /**
     * 检查是否匹配的具体实现，由子类实现
     *
     * @param array $waitCheckValues
     * @param array $checkTarget
     * @return bool
     */
    abstract protected function checkMatch(array $waitCheckValues, array $checkTarget): bool;

    /**
     * 按块分析.
     *
     * @param array $chunk 待分析数据块
     * @param int $analyzePeriods 分析期数
     * @param int $combinationSize 待分析数据块的组合大小
     * @return array
     */
    protected function analyzeChunk(array $chunk, int $analyzePeriods, int $combinationSize): array
    {
        // 排序
        ksort($chunk);

        // 分析期数
        $patternData = array_slice($chunk, 0, $analyzePeriods, true);

        // 排列组合
        $combinations = ArrayHelper::generateCrossGroupCombinations($patternData, $combinationSize);

        // 预测期数据
        $nextData = array_slice($chunk, $analyzePeriods, 1, true);

        return $this->checkCombinationsAgainstNext($combinations, reset($nextData));
    }

    /**
     * 返回组合中与预测结果对比的数组.
     *
     * @param array $combinations 组合列表
     * @param array $nextData 预测数据
     * @return array
     */
    protected function checkCombinationsAgainstNext(array $combinations, array $nextData): array
    {
        $result = [];
        foreach ($combinations as $key => $values) {
            // 由子类实现具体的匹配逻辑
            if ($this->isCombinationMatch($values, $nextData)) {
                $result[$key] = implode('|', $values);
            }
        }
        return $result;
    }

    /**
     * 检查组合是否匹配，由子类实现具体逻辑
     *
     * @param array $values 组合值
     * @param array $nextData 预测数据
     * @return bool
     */
    abstract protected function isCombinationMatch(array $values, array $nextData): bool;
}
