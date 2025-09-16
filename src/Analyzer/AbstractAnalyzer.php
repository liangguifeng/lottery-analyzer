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
}
