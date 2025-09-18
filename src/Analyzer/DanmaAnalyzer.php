<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

use Liangguifeng\LotteryAnalyzer\Support\ArrayHelper;

/**
 * 胆码规律分析：最后一组本期至少一位上奖.
 */
class DanmaAnalyzer extends AbstractAnalyzer
{
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

        // 拆分为规律区间
        $chunks = array_slice(ArrayHelper::chuck($analyzerData, $analyzePeriods + $intervalPeriods + 1), 0, $minConsecutive);

        // 连续命中结果
        $hitLists = array_map(fn ($chunk) => $this->analyzeChunk($chunk, $analyzePeriods, $combinationSize), $chunks);

        // 满足最小连续命中期数的结果
        $result = $this->intersectHitResults($hitLists);

        return [
            'periods' => $analyzePeriods,
            'min_consecutive' => $minConsecutive,
            'combination_size' => $combinationSize,
            'hit_count' => count($result),
            'hit_list' => $this->formatResult($result, $analyzePeriods, $minConsecutive, $intervalPeriods),
        ];
    }

    /**
     * 检查组合是否匹配（胆码：至少有一个数字相交）
     *
     * @param array $values 组合值
     * @param array $nextData 预测数据
     * @return bool
     */
    protected function isCombinationMatch(array $values, array $nextData): bool
    {
        // 组合的数据中，如果有和预测结果产生交集的则命中
        return !empty(array_intersect($values, $nextData));
    }

    /**
     * 检查块是否匹配（胆码：至少有一个数字相交）
     *
     * @param array $waitCheckValues
     * @param array $checkTarget
     * @return bool
     */
    protected function checkMatch(array $waitCheckValues, array $checkTarget): bool
    {
        return !empty(array_intersect($waitCheckValues, $checkTarget));
    }
}
