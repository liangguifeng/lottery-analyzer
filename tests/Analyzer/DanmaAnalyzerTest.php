<?php

namespace Analyzer;

use Liangguifeng\LotteryAnalyzer\Analyzer\DanmaAnalyzer;
use PHPUnit\Framework\TestCase;

class DanmaAnalyzerTest extends TestCase
{
    protected array $history = [
        '2025219' => [9, 0, 0],
        '2025220' => [5, 3, 6],
        '2025221' => [2, 9, 6],
        '2025222' => [0, 1, 0],
        '2025223' => [7, 3, 6],
        '2025224' => [9, 9, 0],
        '2025225' => [8, 0, 6],
        '2025226' => [1, 8, 0],
        '2025227' => [6, 1, 1],
        '2025228' => [4, 6, 5],
        '2025229' => [1, 0, 8],
        '2025230' => [4, 3, 9],
        '2025231' => [7, 3, 2],
        '2025232' => [0, 4, 4],
        '2025233' => [2, 5, 9],
        '2025234' => [6, 2, 0],
        '2025235' => [9, 6, 9],
        '2025236' => [3, 5, 5],
        '2025237' => [5, 9, 0],
        '2025238' => [8, 6, 0],
        '2025239' => [1, 2, 5],
        '2025240' => [4, 1, 9],
        '2025241' => [1, 6, 0],
        '2025242' => [9, 9, 0],
        '2025243' => [1, 7, 6],
        '2025244' => [6, 7, 3],
        '2025245' => [8, 4, 8],
        '2025246' => [9, 9, 7],
        '2025247' => [2, 6, 2],
    ];

    public function testGet()
    {
        $analyzer = new DanmaAnalyzer(array_reverse($this->history, true));
        $result = $analyzer->get();
        $this->assertIsArray($result, "预测结果应为数组");
        $this->assertNotEmpty($result, "预测结果不应为空");
        $this->assertCount(28, $result, "预测结果数量正确");
    }
}
