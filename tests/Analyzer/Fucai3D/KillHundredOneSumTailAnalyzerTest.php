<?php

declare(strict_types=1);

namespace Tests\Analyzer\Fucai3D;

use Liangguifeng\LotteryAnalyzer\Analyzer\KillHundredOneSumTailAnalyzer;

/**
 * 【杀百个和尾】测试用例.
 *
 * @internal
 * @coversNothing
 */
class KillHundredOneSumTailAnalyzerTest extends BaseFucai3DTest
{
    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new KillHundredOneSumTailAnalyzer($this->history);
    }

    /**
     * 分析测试.
     */
    public function testAnalyze()
    {
        $analyzePeriods = 3;
        $consecutive = 30;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive);
        $this->assertNotEmpty($result);
        $this->assertIsArray($result);
        $this->assertCount(4, $result['hit_list']);
    }

    /**
     * 返回最大命中期数分析测试.
     */
    public function testAnalyzeWithMaxConsecutive()
    {
        $analyzePeriods = 3;
        $consecutive = 39;
        $result = $this->analyzer->withMaxConsecutive(true)->analyze($analyzePeriods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(2, $result['hit_list']);
        $this->assertEquals(39, $result['hit_list'][0]['max_consecutive']);
        $this->assertEquals(39, $result['hit_list'][1]['max_consecutive']);
    }

    /**
     * 返回间隔期数分析测试.
     */
    public function testAnalyzeByIntervalPeriods()
    {
        $analyzePeriods = 3;
        $consecutive = 39;
        $combinationSize = 3;
        $intervalPeriods = 2;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive, $combinationSize, $intervalPeriods);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(2, $result['hit_list']);

        // 倒数第一期预测
        $endFirstPeriod = $analyzePeriods + $intervalPeriods + 1;
        $predict1 =array_slice($result['hit_list'][0]['items'], -$endFirstPeriod, 1)[0];
        $this->assertEquals(true, $predict1['is_predict']);

        // 倒数第二期预测
        $endSecondPeriod = 2 * $endFirstPeriod;
        $predict2 =array_slice($result['hit_list'][0]['items'], -$endSecondPeriod, 1)[0];
        $this->assertEquals(true, $predict2['is_predict']);

        // 倒数第三期预测
        $endSecondPeriod = 3 * $endFirstPeriod;
        $predict2 =array_slice($result['hit_list'][0]['items'], -$endSecondPeriod, 1)[0];
        $this->assertEquals(true, $predict2['is_predict']);
    }

    /**
     * 组合长度分析测试.
     */
    public function testCombinationSizeAnalyze()
    {
        $analyzePeriods = 3;
        $consecutive = 30;
        $combinationSize = 4;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive, $combinationSize);
        $this->assertNotEmpty($result);
        $this->assertIsArray($result);
        $this->assertCount(8, $result['hit_list']);
    }

    /**
     * 压力测试.
     */
    public function testStressTest()
    {
        // 因为组合大，所以允许久一点
        $this->maxExecutionTime = 10.0;

        $analyzePeriods = 10; // 分析期数
        $consecutive = 50; // 最大连续命中期数
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive);
        $this->assertNotEmpty($result);
        $this->assertIsArray($result);
    }
}
