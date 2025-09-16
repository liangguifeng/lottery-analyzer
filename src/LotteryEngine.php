<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer;

use Liangguifeng\LotteryAnalyzer\Analyzer\DanmaAnalyzer;
use Liangguifeng\LotteryAnalyzer\Enum\DigitLength;
use Liangguifeng\LotteryAnalyzer\Enum\LotteryType;
use Liangguifeng\LotteryAnalyzer\Enum\Position;
use Liangguifeng\LotteryAnalyzer\Enum\RuleType;
use RuntimeException;

class LotteryEngine
{
    protected array $history = [];

    protected int $periods = 5;

    protected int $minConsecutive = 1;

    protected ?RuleType $ruleType = null;

    protected array $ruleMap = [];

    public function __construct(array $history, int $periods = 5, int $minConsecutive = 1)
    {
        $this->history = $history;
        $this->periods = $periods;
        $this->minConsecutive = $minConsecutive;

        // 规则类型映射到对应 Analyzer 类
        $this->ruleMap = [
            RuleType::DANMA->value => DanmaAnalyzer::class,
            // TODO: 添加其他规则
            // RuleType::DUDAN->value => DudanAnalyzer::class,
        ];
    }

    /**
     * 选择规律类型.
     */
    public function useRule(RuleType $ruleType): self
    {
        $this->ruleType = $ruleType;
        return $this;
    }

    /**
     * 执行预测.
     */
    public function predict(
        LotteryType $lotteryType,
        DigitLength $digitLength,
        Position $position
    ): array {
        if (!$this->ruleType) {
            throw new RuntimeException('Please select a rule type first using useRule().');
        }

        $ruleClass = $this->ruleMap[$this->ruleType->value] ?? null;
        if (!$ruleClass) {
            throw new RuntimeException("Analyzer for rule {$this->ruleType->value} not found.");
        }

        // 实例化 Analyzer
        $analyzer = new $ruleClass(
            history: $this->history,
            periods: $this->periods,
            minConsecutive: $this->minConsecutive
        );

        // 调用 Analyzer 的 predict 方法
        return $analyzer->predict($position, $lotteryType, $digitLength);
    }
}
