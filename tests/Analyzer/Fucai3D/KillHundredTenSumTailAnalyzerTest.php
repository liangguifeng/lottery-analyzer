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
        $analyzePeriods = 3;
        $consecutive = 40;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(2, $result['hit_list']);
    }

    /**
     * 返回最大命中期数分析测试.
     */
    public function testAnalyzeWithMaxConsecutive()
    {
        $analyzePeriods = 3;
        $consecutive = 52;
        $result = $this->analyzer->withMaxConsecutive(true)->analyze($analyzePeriods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(1, $result['hit_list']);
        $this->assertEquals(52, $result['hit_list'][0]['max_consecutive']);
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
        $endFirstPeriod = $analyzePeriods + 1;
        $predict1 =array_slice($result['hit_list'][0]['items'], -$endFirstPeriod, 1)[0];
        $this->assertEquals(true, $predict1['is_predict']);

        // 倒数第二期预测
        $endSecondPeriod = $endFirstPeriod + $analyzePeriods + $intervalPeriods + 1;
        $predict2 =array_slice($result['hit_list'][0]['items'], -$endSecondPeriod, 1)[0];
        $this->assertEquals(true, $predict2['is_predict']);
    }

    /**
     * 组合长度分析测试.
     */
    public function testCombinationSizeAnalyze(): void
    {
        $analyzePeriods = 3;
        $consecutive = 30;
        $combinationSize = 4;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive, $combinationSize);
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

        $analyzePeriods = 10; // 分析期数
        $consecutive = 50; // 最大连续命中期数
        $combinationSize = 3; // 最大组合数字长度
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
