<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

use Liangguifeng\LotteryAnalyzer\Support\ArrayHelper;

/**
 * 杀十个和尾：预测数字之和尾数，一定不与十位、个位之和尾数相同.
 */
class KillTenOneSumTailAnalyzer extends SumTailAnalyzer implements AnalyzerInterface
{
    /**
     * 百位个位路径标定.
     *
     * @var int[]
     */
    private $killPath = [2, 3];

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
     * 获取杀数路径.
     *
     * @return int[]
     */
    protected function getKillPath(): array
    {
        return $this->killPath;
    }
}
