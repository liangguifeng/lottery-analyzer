<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

use Liangguifeng\LotteryAnalyzer\Support\ArrayHelper;

/**
 * 毒胆码规律分析：预测结果数组中的数字，不可能上奖(随着组合大小增大，概率越小).
 */
class DudanAnalyzer extends AbstractAnalyzer
{
    /**
     * 分析.
     *
     * @param int $periods 间隔期数
     * @param int $minConsecutive 最小连续命中期数
     * @param int $combinationSize 组合大小
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
            'hit_list' => $this->formatResult($result, $periods, $minConsecutive),
        ];
    }

    /**
     * 检查组合是否匹配（毒胆码：没有任何数字相交）
     *
     * @param array $values 组合值
     * @param array $nextData 预测数据
     * @return bool
     */
    protected function isCombinationMatch(array $values, array $nextData): bool
    {
        // 组合的数据中，如果没有和预测结果产生交集的则命中
        return empty(array_intersect($values, $nextData));
    }

    /**
     * 检查块是否匹配（毒胆码：没有任何数字相交）
     *
     * @param array $waitCheckValues
     * @param array $checkTarget
     * @return bool
     */
    protected function checkMatch(array $waitCheckValues, array $checkTarget): bool
    {
        return empty(array_intersect($waitCheckValues, $checkTarget));
    }
}
