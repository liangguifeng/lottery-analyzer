<?php

declare(strict_types=1);

namespace Tests\Analyzer\Fucai3D;

use Liangguifeng\LotteryAnalyzer\Analyzer\KillHundredTenSumTailAnalyzer;

/**
 * 【杀百十和尾】测试用例.
 */
class KillHundredTenSumTailAnalyzerTest extends BaseFucai3DTest
{
    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new KillHundredTenSumTailAnalyzer($this->history);
    }

    /**
     * 分析测试.
     *
     * @return void
     */
    public function testAnalyze() : void
    {
        $periods = 3;
        $consecutive = 40;
        $result = $this->analyzer->analyze($periods, $consecutive);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(2, $result['hit_list']);
    }

    /**
     * 组合长度分析测试.
     *
     * @return void
     */
    public function testCombinationSizeAnalyze() : void
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
     *
     * @return void
     */
    public function testStressTest() : void
    {
        $periods = 10; // 最大间隔期数
        $consecutive = 50; // 最大连续命中期数
        $combinationSize = 3; // 最大组合数字长度
        $result = $this->analyzer->analyze($periods, $consecutive, $combinationSize);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
