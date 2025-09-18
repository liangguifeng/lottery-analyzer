<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

interface AnalyzerInterface
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
    public function analyze(int $analyzePeriods = 3, int $minConsecutive = 3, int $combinationSize = 3, int $intervalPeriods = 0): array;
}
