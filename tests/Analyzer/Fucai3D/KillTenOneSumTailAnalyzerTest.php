<?php

declare(strict_types=1);

namespace Tests\Analyzer\Fucai3D;

use Liangguifeng\LotteryAnalyzer\Analyzer\KillTenOneSumTailAnalyzer;

/**
 * 【杀十个和尾】测试用例.
 *
 * @coversNothing
 * @internal
 */
class KillTenOneSumTailAnalyzerTest extends BaseFucai3DTest
{
    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new KillTenOneSumTailAnalyzer($this->history);
    }

    /**
     * 分析测试.
     */
    public function testAnalyze()
    {
        $periods = 3;
        $consecutive = 30;
        $result = $this->analyzer->analyze($periods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(5, $result['hit_list']);
    }

    /**
     * 组合长度分析测试.
     */
    public function testCombinationSizeAnalyze()
    {
        $periods = 3;
        $consecutive = 30;
        $combinationSize = 4;
        $result = $this->analyzer->analyze($periods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(6, $result['hit_list']);
    }

    /**
     * 压力测试.
     */
    public function testStressTest()
    {
        $periods = 10; // 最大间隔期数
        $consecutive = 50; // 最大连续命中期数
        $result = $this->analyzer->analyze($periods, $consecutive);
        $this->assertIsArray($result, sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 预测结果应为数组', $periods, $consecutive));
        $this->assertNotEmpty($result, sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 预测结果不应为空', $periods, $consecutive));
    }
}
