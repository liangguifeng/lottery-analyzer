<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

interface AnalyzerInterface
{
    /**
     * 是否返回最大连续期数.
     *
     * @param bool $withMaxConsecutive
     * @return static
     */
    public function withMaxConsecutive(bool $withMaxConsecutive): static;

    /**
     * 分析.
     *
     * @param int $periods 间隔期数
     * @param int $minConsecutive 最小连续命中期数
     * @param int $combinationSize 组合大小
     * @return array
     */
    public function analyze(int $periods = 3, int $minConsecutive = 3, int $combinationSize = 3): array;
}
