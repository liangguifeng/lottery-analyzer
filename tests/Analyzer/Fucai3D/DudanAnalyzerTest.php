<?php

declare(strict_types=1);

namespace Tests\Analyzer\Fucai3D;

use Liangguifeng\LotteryAnalyzer\Analyzer\DudanAnalyzer;

/**
 * 毒胆码测试用例.
 *
 * @internal
 * @coversNothing
 */
class DudanAnalyzerTest extends BaseFucai3DTest
{
    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new DudanAnalyzer($this->history);
    }

    /**
     * 分析测试.
     */
    public function testAnalyze()
    {
        $analyzePeriods = 3;
        $consecutive = 5;
        $combinationSize = 1;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result['hit_list']);
    }

    /**
     * 返回最大命中期数分析测试.
     */
    public function testAnalyzeWithMaxConsecutive()
    {
        $analyzePeriods = 3;
        $consecutive = 11;
        $combinationSize = 1;
        $result = $this->analyzer->withMaxConsecutive(true)->analyze($analyzePeriods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(1, $result['hit_list']);
        $this->assertEquals(11, $result['hit_list'][0]['max_consecutive']);
    }

    /**
     * 返回间隔期数分析测试.
     */
    public function testAnalyzeByIntervalPeriods()
    {
        $analyzePeriods = 3;
        $consecutive = 5;
        $combinationSize = 3;
        $intervalPeriods = 2;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive, $combinationSize, $intervalPeriods);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(1, $result['hit_list']);

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
    public function testCombinationSizeAnalyze()
    {
        $analyzePeriods = 3;
        $consecutive = 5;
        $combinationSize = 2;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result['hit_list']);
    }

    /**
     * 压力测试.
     */
    public function testStressTest()
    {
        $analyzePeriods = 10; // 分析期数
        $consecutive = 50; // 最大连续命中期数
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
