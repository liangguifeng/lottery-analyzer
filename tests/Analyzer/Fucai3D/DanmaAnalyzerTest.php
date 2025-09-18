<?php

declare(strict_types=1);

namespace Tests\Analyzer\Fucai3D;

use Liangguifeng\LotteryAnalyzer\Analyzer\DanmaAnalyzer;

/**
 * 胆码测试用例.
 *
 * @internal
 * @coversNothing
 */
class DanmaAnalyzerTest extends BaseFucai3DTest
{
    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new DanmaAnalyzer($this->history);
    }

    /**
     * 分析测试.
     */
    public function testAnalyze()
    {
        $analyzePeriods = 3;
        $consecutive = 7;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result['hit_list']);
    }

    /**
     * 返回最大命中期数分析测试.
     */
    public function testAnalyzeWithMaxConsecutive()
    {
        $analyzePeriods = 3;
        $consecutive = 7;
        $result = $this->analyzer->withMaxConsecutive(true)->analyze($analyzePeriods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result['hit_list']);
        $this->assertEquals(7, $result['hit_list'][0]['max_consecutive']);
        $this->assertEquals(7, $result['hit_list'][1]['max_consecutive']);
        $this->assertEquals(7, $result['hit_list'][2]['max_consecutive']);
        $this->assertEquals(7, $result['hit_list'][3]['max_consecutive']);
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
    public function testCombinationSizeAnalyze()
    {
        $analyzePeriods = 3;
        $consecutive = 10;
        $combinationSize = 4;
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result['hit_list']);
    }

    /**
     * 压力测试.
     */
    public function testStressTest()
    {
        $analyzePeriods = 10; // 最大间隔期数
        $consecutive = 50; // 最大连续命中期数
        $result = $this->analyzer->analyze($analyzePeriods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
