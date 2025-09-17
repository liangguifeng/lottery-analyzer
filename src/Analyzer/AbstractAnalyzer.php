<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

abstract class AbstractAnalyzer
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
}