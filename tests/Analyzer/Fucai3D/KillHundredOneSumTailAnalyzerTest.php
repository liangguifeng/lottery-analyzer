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
     *
     * @return void
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
        $periods = 3;
        $consecutive = 7;
        $result = $this->analyzer->analyze($periods, $consecutive);
        $this->assertIsArray($result, sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 预测结果应为数组', $periods, $consecutive));
        $this->assertNotEmpty($result, sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 预测结果不应为空', $periods, $consecutive));
        $this->assertNotEmpty($result['hit_list'], sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 预测结果中命中的结果集不应为空', $periods, $consecutive));
        $this->assertCount(4, $result['hit_list'], sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 预测结果中命中的结果集长度应等于4', $periods, $consecutive));
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
        $this->assertIsArray($result, sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 组合数字长度：%s, 预测结果应为数组', $periods, $consecutive, $combinationSize));
        $this->assertNotEmpty($result, sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s,  组合数字长度：%s, 预测结果不应为空', $periods, $consecutive, $combinationSize));
        $this->assertNotEmpty($result['hit_list'], sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 组合数字长度：%s, 预测结果中命中的结果集不应为空', $periods, $consecutive, $combinationSize));
        $this->assertCount(4, $result['hit_list'], sprintf('当前测试的是: 间隔期数：%s, 最小连续命中期数：%s, 组合数字长度：%s, 预测结果中命中的结果集长度应等于4', $periods, $consecutive, $combinationSize));
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
