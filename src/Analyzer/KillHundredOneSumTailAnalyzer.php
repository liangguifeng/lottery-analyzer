<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

use Liangguifeng\LotteryAnalyzer\Support\ArrayHelper;

/**
 * 杀百个和尾：预测数字之和尾数，一定不与百位、个位之和尾数相同.
 */
class KillHundredOneSumTailAnalyzer extends SumTailAnalyzer
{
    /**
     * 百位个位路径标定.
     *
     * @var int[]
     */
    private $killPath = [1, 3];

    /**
     * 分析.
     *
     * @param int $analyzePeriods 分析期数(如3期，则是3期的分析预测下1期)
     * @param int $minConsecutive 最小连续命中期数
     * @param int $combinationSize 组合大小
     * @param int $intervalPeriods 间隔期数(上一次分析到下一次预测开始的间隔，默认不间隔)
     * @return array
     */
    public function analyze(int $analyzePeriods = 3, int $minConsecutive = 3, int $combinationSize = 3, int $intervalPeriods = 0): array
    {
        // 分析数据(剔除预测数据的结果集)
        $analyzerData = $this->getAnalyzerData($analyzePeriods);

        // 拆分为规律区间，屏蔽间隔期数数据
        $chunks = ArrayHelper::chuck($analyzerData, $analyzePeriods + $intervalPeriods + 1);
        foreach ($chunks as &$chunk) {
            $chunk = array_slice($chunk, 0, $analyzePeriods + 1, true);
        }

        // 需要分析的块
        $analyzerChunks = array_slice($chunks, 0, $minConsecutive);

        // 连续命中结果
        $hitLists = array_map(fn ($chunk) => $this->analyzeChunk($chunk, $analyzePeriods, $combinationSize), $analyzerChunks);

        // 满足最小连续命中期数的结果
        $result = $this->intersectHitResults($hitLists);

        return [
            'analyze_periods' => $analyzePeriods,
            'interval_periods' => $intervalPeriods,
            'min_consecutive' => $minConsecutive,
            'combination_size' => $combinationSize,
            'hit_count' => count($result),
            'hit_list' => $this->formatResult($result, $analyzePeriods, $minConsecutive, $intervalPeriods),
        ];
    }

    /**
     * 获取杀数路径.
     *
     * @return int[]
     */
    protected function getKillPath(): array
    {
        return $this->killPath;
    }
}
