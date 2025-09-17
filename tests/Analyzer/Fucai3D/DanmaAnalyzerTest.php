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
        $periods = 3;
        $consecutive = 7;
        $result = $this->analyzer->analyze($periods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result['hit_list']);
    }

    /**
     * 返回最大命中期数分析测试.
     */
    public function testAnalyzeWithMaxConsecutive()
    {
        $periods = 3;
        $consecutive = 7;
        $result = $this->analyzer->withMaxConsecutive(true)->analyze($periods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result['hit_list']);
        $this->assertEquals(7, $result['hit_list'][0]['max_consecutive']);
        $this->assertEquals(7, $result['hit_list'][1]['max_consecutive']);
        $this->assertEquals(7, $result['hit_list'][2]['max_consecutive']);
        $this->assertEquals(7, $result['hit_list'][3]['max_consecutive']);
    }

    /**
     * 组合长度分析测试.
     */
    public function testCombinationSizeAnalyze()
    {
        $periods = 3;
        $consecutive = 10;
        $combinationSize = 4;
        $result = $this->analyzer->analyze($periods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result['hit_list']);
    }

    /**
     * 压力测试.
     */
    public function testStressTest()
    {
        $periods = 10; // 最大间隔期数
        $consecutive = 50; // 最大连续命中期数
        $result = $this->analyzer->analyze($periods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
