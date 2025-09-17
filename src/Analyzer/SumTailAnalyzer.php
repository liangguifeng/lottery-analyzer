<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

use Liangguifeng\LotteryAnalyzer\Support\ArrayHelper;

/**
 * 和尾分析器抽象类
 */
abstract class SumTailAnalyzer extends AbstractAnalyzer
{
    /**
     * 杀数路径标定
     *
     * @var int[]
     */
    abstract protected function getKillPath(): array;

    /**
     * 检查组合是否匹配（和尾：组合数字之和尾数不等于指定位置数字之和尾数）
     *
     * @param array $values 组合值
     * @param array $nextData 预测数据
     * @return bool
     */
    protected function isCombinationMatch(array $values, array $nextData): bool
    {
        $preTail = array_sum($values) % 10;
        $nextTail = array_sum(ArrayHelper::getByPath($nextData, $this->getKillPath())) % 10;

        // 如果组合数据相加的尾数 不等于 预测数据杀数位置之和的尾数，则算命中
        return $preTail != $nextTail;
    }

    /**
     * 检查块是否匹配（和尾：组合数字之和尾数不等于指定位置数字之和尾数）
     *
     * @param array $waitCheckValues
     * @param array $checkTarget
     * @return bool
     */
    protected function checkMatch(array $waitCheckValues, array $checkTarget): bool
    {
        $preTail = array_sum($waitCheckValues) % 10;
        $nextTail = array_sum(ArrayHelper::getByPath($checkTarget, $this->getKillPath())) % 10;

        return $preTail != $nextTail;
    }
}
