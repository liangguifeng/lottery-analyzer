<?php

declare(strict_types=1);

namespace Tests\Analyzer\Fucai3D;

use Liangguifeng\LotteryAnalyzer\Analyzer\KillHundredTenSumTailAnalyzer;

/**
 * 【杀百十和尾】测试用例.
 * @internal
 * @coversNothing
 */
class KillHundredTenSumTailAnalyzerTest extends BaseFucai3DTest
{
    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new KillHundredTenSumTailAnalyzer($this->history);
    }

    /**
     * 分析测试.
     */
    public function testAnalyze(): void
    {
        $periods = 3;
        $consecutive = 40;
        $result = $this->analyzer->analyze($periods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(2, $result['hit_list']);
    }

    /**
     * 返回最大命中期数分析测试.
     */
    public function testAnalyzeWithMaxConsecutive()
    {
        $periods = 3;
        $consecutive = 52;
        $result = $this->analyzer->withMaxConsecutive(true)->analyze($periods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(1, $result['hit_list']);
        $this->assertEquals(52, $result['hit_list'][0]['max_consecutive']);
    }

    /**
     * 组合长度分析测试.
     */
    public function testCombinationSizeAnalyze(): void
    {
        $periods = 3;
        $consecutive = 30;
        $combinationSize = 4;
        $result = $this->analyzer->analyze($periods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result['hit_list']);
    }

    /**
     * 压力测试.
     */
    public function testStressTest(): void
    {
        // 因为组合大，所以允许久一点
        $this->maxExecutionTime = 3.0;

        $periods = 10; // 最大间隔期数
        $consecutive = 50; // 最大连续命中期数
        $combinationSize = 3; // 最大组合数字长度
        $result = $this->analyzer->analyze($periods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
