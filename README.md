# Lottery Analyzer 彩票分析器

彩票分析器是一个用于分析彩票开奖数据规律的PHP库，特别适用于福彩3D等数字型彩票。通过多种分析算法，帮助用户发现潜在的开奖规律。

## 功能特性

- 胆码规律分析：预测下一期可能出现的数字
- 毒胆规律分析：预测下一期不可能出现的数字
- 和尾规律分析：基于数字和尾的规律进行分析
- 多种分析维度：支持自定义分析周期和组合大小
- 灵活配置：可自定义最小连续命中次数等参数

## 安装

使用 Composer 安装：

```
composer require liangguifeng/lottery-analyzer
```

或者克隆项目：

```
git clone https://github.com/liangguifeng/lottery-analyzer.git
cd lottery-analyzer
composer install
```

## 快速开始

```php
<?php
require_once 'vendor/autoload.php';

use Liangguifeng\LotteryAnalyzer\Analyzer\DanmaAnalyzer;

// 准备历史开奖数据（期号 => [百位, 十位, 个位]）
$historyData = [
    '2024001' => [1, 2, 3],
    '2024002' => [4, 5, 6],
    '2024003' => [7, 8, 9],
    // ... 更多数据
];

// 创建分析器实例
$analyzer = new DanmaAnalyzer($historyData);

// 执行分析
$result = $analyzer->analyze();

// 输出结果
print_r($result);
```

## 核心概念

### 历史数据格式

历史数据必须是关联数组，格式如下：

```php
$historyData = [
    '期号' => [百位数字, 十位数字, 个位数字],
    // 例如：
    '2024001' => [1, 2, 3],
    '2024002' => [4, 5, 6],
];
```

### 分析参数

所有分析器都支持以下四个核心参数：

1. **analyzePeriods（分析期数）**：用于分析规律的历史期数
2. **minConsecutive（最小连续命中期数）**：规律需要连续命中的最小期数
3. **combinationSize（组合大小）**：从每期数据中选取的数字个数
4. **intervalPeriods（间隔期数）**：上一次分析到下一次预测开始的间隔

## 分析器类型

### 胆码分析器 (DanmaAnalyzer)

胆码分析用于找出下一期可能出现的数字。其原理是：规律路径指定位置的数字至少有一个会在下一期出现。

```php
use Liangguifeng\LotteryAnalyzer\Analyzer\DanmaAnalyzer;

$analyzer = new DanmaAnalyzer($historyData);
$result = $analyzer->analyze(3, 3, 3, 0); // analyzePeriods, minConsecutive, combinationSize, intervalPeriods
```

### 毒胆分析器 (DudanAnalyzer)

毒胆分析用于找出下一期不可能出现的数字。其原理是：规律路径指定位置的数字在下一期不会出现。

```php
use Liangguifeng\LotteryAnalyzer\Analyzer\DudanAnalyzer;

$analyzer = new DudanAnalyzer($historyData);
$result = $analyzer->analyze(3, 3, 3, 0);
```

### 和尾分析器 (SumTailAnalyzer)

和尾分析基于数字和尾数的规律进行预测，包括三种类型：

#### 杀百个和尾分析器 (KillHundredOneSumTailAnalyzer)

预测数字之和尾数不与百位、个位之和尾数相同。

```php
use Liangguifeng\LotteryAnalyzer\Analyzer\KillHundredOneSumTailAnalyzer;

$analyzer = new KillHundredOneSumTailAnalyzer($historyData);
$result = $analyzer->analyze(3, 3, 3, 0);
```

#### 杀百十和尾分析器 (KillHundredTenSumTailAnalyzer)

预测数字之和尾数不与百位、十位之和尾数相同。

```php
use Liangguifeng\LotteryAnalyzer\Analyzer\KillHundredTenSumTailAnalyzer;

$analyzer = new KillHundredTenSumTailAnalyzer($historyData);
$result = $analyzer->analyze(3, 3, 3, 0);
```

#### 杀十个和尾分析器 (KillTenOneSumTailAnalyzer)

预测数字之和尾数不与十位、个位之和尾数相同。

```php
use Liangguifeng\LotteryAnalyzer\Analyzer\KillTenOneSumTailAnalyzer;

$analyzer = new KillTenOneSumTailAnalyzer($historyData);
$result = $analyzer->analyze(3, 3, 3, 0);
```

## 高级用法

### 获取最大连续命中期数

```php
$analyzer = new DanmaAnalyzer($historyData);
$result = $analyzer->withMaxConsecutive(true)->analyze(3, 3, 3, 0);
```

### 自定义参数

```php
// 使用不同的参数组合
$result = $analyzer->analyze(
    analyzePeriods: 5,   // 分析最近5期数据
    minConsecutive: 4,   // 要求至少连续4期命中
    combinationSize: 2,  // 使用2个数字的组合
    intervalPeriods: 0   // 间隔期数
);
```

## 结果说明

分析结果包含以下字段：

```php
[
    'analyze_periods' => 3,        // 分析期数
    'interval_periods' => 3,       // 间隔期数
    'min_consecutive' => 3,        // 最小连续命中期数
    'combination_size' => 3,       // 组合大小
    'hit_count' => 5,              // 命中规律数量
    'hit_list' => [                // 命中规律列表
        [
            'path_string' => '1_1|2_2',  // 规律路径字符串表示
            'path' => ['1_1', '2_2'],    // 规律路径数组表示
            'max_consecutive' => 8,      // 最大连续命中次数
            'items' => [                 // 规律详情
                // ... 详细数据
            ]
        ],
        // ... 更多规律
    ]
]
```

### 规律路径说明

规律路径用于描述规律的位置信息，格式为 |组号_位置号|，例如：

- |1_1| 表示第1组第1个位置的数字
- |2_3| 表示第2组第3个位置的数字

## 完整使用示例

```php
<?php
require_once 'vendor/autoload.php';

use Liangguifeng\LotteryAnalyzer\Analyzer\DanmaAnalyzer;
use Liangguifeng\LotteryAnalyzer\Analyzer\DudanAnalyzer;
use Liangguifeng\LotteryAnalyzer\Analyzer\KillHundredOneSumTailAnalyzer;

// 准备历史数据（至少需要几十期数据才能得到有意义的结果）
$historyData = [
    '2024001' => [1, 2, 3],
    '2024002' => [4, 5, 6],
    '2024003' => [7, 8, 9],
    '2024004' => [2, 3, 4],
    '2024005' => [5, 6, 7],
    '2024006' => [8, 9, 0],
    // ... 更多数据
];

// 1. 胆码分析
echo "=== 胆码分析 ===\n";
$danmaAnalyzer = new DanmaAnalyzer($historyData);
$danmaResult = $danmaAnalyzer->analyze(3, 3, 3);
echo "找到 " . $danmaResult['hit_count'] . " 个规律\n";

// 2. 毒胆分析
echo "\n=== 毒胆分析 ===\n";
$dudanAnalyzer = new DudanAnalyzer($historyData);
$dudanResult = $dudanAnalyzer->analyze(3, 3, 3);
echo "找到 " . $dudanResult['hit_count'] . " 个规律\n";

// 3. 杀百个和尾分析
echo "\n=== 杀百个和尾分析 ===\n";
$killAnalyzer = new KillHundredOneSumTailAnalyzer($historyData);
$killResult = $killAnalyzer->withMaxConsecutive(true)->analyze(3, 3, 3);
echo "找到 " . $killResult['hit_count'] . " 个规律\n";

// 输出详细结果
foreach ($killResult['hit_list'] as $hit) {
    echo "规律路径: " . $hit['path_string'] . ", 最大连续命中: " . $hit['max_consecutive'] . "\n";
}
```

## 性能优化建议

1. **合理设置参数**：避免设置过大的|analyzePeriods|和|combinationSize|值
2. **数据量控制**：使用适量的历史数据，通常50-200期较为合适
3. **批量处理**：对于大量数据，考虑分批处理

## 注意事项

1. 历史数据需要按照期号正确排序
2. 数据量越大，分析结果通常越准确
3. 不同的参数组合会产生不同的分析结果
4. 彩票开奖具有随机性，分析结果仅供参考，不能保证100%准确
5. 建议结合多种分析方法综合判断

# 由 JetBrains 支持的项目
非常感谢 JetBrains 向我提供了执照，可以从事该项目和其他开源项目。

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/liangguifeng)

## License

MIT License