<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

use Liangguifeng\LotteryAnalyzer\Support\ArrayHelper;

/**
 * 毒胆码规律分析：预测结果数组中的数字，不可能上奖(随着组合大小增大，概率越小).
 */
class DudanAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
    /**
     * 分析.
     *
     * @param int $periods 间隔期数
     * @param int $minConsecutive 最小连续命中期数
     * @param string $combinationSize 组合大小
     * @return array
     */
    public function analyze(int $periods = 3, int $minConsecutive = 3, int $combinationSize = 3): array
    {
        // 分析数据(剔除预测数据的结果集)
        $analyzerData = $this->getAnalyzerData($periods);

        // 拆分为规律区间
        $chunks = array_slice(ArrayHelper::chuck($analyzerData, $periods + 1), 0, $minConsecutive);

        // 连续命中结果
        $hitLists = array_map(fn ($chunk) => $this->analyzeChunk($chunk, $periods, $combinationSize), $chunks);

        // 满足最小连续命中期数的结果
        $result = $this->intersectHitResults($hitLists);

        return [
            'periods' => $periods,
            'min_consecutive' => $minConsecutive,
            'combination_size' => $combinationSize,
            'hit_count' => count($result),
            'hit_list' => $this->formatResult($result, $periods),
        ];
    }

    /**
     * 根据命中路径，处理历史数据.
     *
     * @param array $history 历史数据
     * @param int $periods 间隔期数
     * @param string $path 命中的具体坐标
     * @return array
     */
    private function processHistory(array $history, int $periods, string $path): array
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

        // 第一个分片：取 periods 个
        $take = min($periods, $cursor);
        $start = $cursor - $take;
        $groups[] = array_slice($keys, $start, $take); // 保持升序
        $cursor -= $take;

        // 后续每轮取 periods + 1 个
        while ($cursor > 0) {
            $take = min($periods + 1, $cursor);
            $start = $cursor - $take;
            $groups[] = array_slice($keys, $start, $take);
            $cursor -= $take;
        }

        // 3. 解析 path => [ group => [pos, pos] ]
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

        // 去重每个 group 的 pos
        foreach ($coords as $g => $arr) {
            $coords[$g] = array_values(array_unique($arr));
        }

        // 4. 处理每个分片
        $result = [];
        foreach ($groups as $groupKeys) {
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
                    'hit' => array_values($hit),
                    'is_predict' => false,
                ];

                // 预测位标记
                if ($localIndex === $periods) {
                    $result[$periodKey]['is_predict'] = true;
                }
            }
        }

        // 5. 最终按 key 升序返回
        ksort($result);

        return $result;
    }

    /**
     * 获取待分析数据(剔除预测数据的结果集).
     *
     * @param int $periods 间隔期数
     * @return array
     */
    private function getAnalyzerData(int $periods): array
    {
        return array_slice($this->historyData, $periods, null, true);
    }

    /**
     * 按块分析.
     *
     * @param array $chunk 待分析数据块
     * @param int $periods 间隔期数
     * @param int $combinationSize 待分析数据块的组合大小
     * @return array
     */
    private function analyzeChunk(array $chunk, int $periods, int $combinationSize): array
    {
        // 排序，还是一样防呆...
        ksort($chunk);

        // 间隔期数
        $patternData = array_slice($chunk, 0, $periods, true);

        // 排列组合
        $combinations = ArrayHelper::generateCrossGroupCombinations($patternData, $combinationSize);

        // 预测期数据
        $nextData = array_slice($chunk, $periods, $periods + 1, true);

        return $this->checkCombinationsAgainstNext($combinations, reset($nextData));
    }

    /**
     * 返回组合中与预测结果没有产生交集的数组.
     *
     * @param array $combinations 组合列表
     * @param array $nextData 预测数据
     * @return array
     */
    private function checkCombinationsAgainstNext(array $combinations, array $nextData): array
    {
        $result = [];
        foreach ($combinations as $key => $values) {
            // 组合的数据中，如果没有和预测结果产生交集的则命中
            if (!array_intersect($values, $nextData)) {
                $result[$key] = implode('|', $values);
            }
        }
        return $result;
    }

    /**
     * 获取结果集的交集.
     *
     * @param array $hitLists 所有分块命中的结果集
     * @return array
     */
    private function intersectHitResults(array $hitLists): array
    {
        if (empty($hitLists)) {
            return [];
        }

        // 获取第一个结果集作为参照物
        $first = array_keys($hitLists[0]);

        return array_values(array_filter($first, function ($key) use ($hitLists) {
            foreach ($hitLists as $hits) {
                // 如果结果集的key不在其他结果集的key中，则剔除
                if (!array_key_exists($key, $hits)) {
                    return false;
                }
            }
            return true;
        }));
    }

    /**
     * 格式化结果集.
     *
     * @param array $paths 命中的全部坐标
     * @param int $periods 间隔期数
     * @return array
     */
    private function formatResult(array $paths, int $periods): array
    {
        $result = [];
        foreach ($paths as $path) {
            $hitList = $this->processHistory($this->historyData, $periods, $path);
            $result[] = [
                'path_string' => $path,
                'path' => explode('|', $path),
                'items' => $hitList,
            ];
        }

        return $result;
    }
}
