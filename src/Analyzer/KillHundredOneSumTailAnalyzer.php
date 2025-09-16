<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

/**
 * 杀百个和尾：预测数字之和尾数，一定不与百位、个位之和尾数相同
 */
class KillHundredOneSumTailAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
    /**
     * 分析.
     *
     * @param int $periods 间隔期数
     * @param int $minConsecutive 最小连续命中期数
     * @param int $combinationSize 组合大小
     * @return array
     */
    public function analyze(int $periods = 3, int $minConsecutive = 3, int $combinationSize = 3)
    {
        // TODO: Implement analyze() method.
    }
}