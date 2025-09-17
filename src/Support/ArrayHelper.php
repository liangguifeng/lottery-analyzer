<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Support;

/**
 * 数组处理工具类.
 */
class ArrayHelper
{
    /**
     * 根据数组path获取新数组.
     *
     * @param array $array
     * @param array $path 非数组下标，而是[1,2,3,4]这种坐标路径
     * @return array
     */
    public static function getByPath(array $array, array $path)
    {
        $result = [];
        foreach ($path as $key) {
            $result[] = $array[$key - 1];
        }
        return $result;
    }

    /**
     * 保留key进行数组分块，元素不足则丢弃.
     *
     * @param array $array
     * @param int $size
     * @return array
     */
    public static function chuck(array $array, int $size): array
    {
        $result = [];
        $temp = [];
        foreach ($array as $k => $v) {
            $temp[$k] = $v;
            if (count($temp) === $size) {
                $result[] = $temp;
                $temp = [];
            }
        }
        return $result;
    }

    /**
     * 生成跨组排列组合.
     *
     * @param array $groups 多组数组
     * @param int $combinationSize 每个组合中选择的元素数量，默认 3
     * @return array
     */
    public static function generateCrossGroupCombinations(array $groups, int $combinationSize = 3): array
    {
        if ($combinationSize <= 0 || empty($groups)) {
            return [];
        }

        // 将原始数组转换为带组号和元素索引的项
        $items = [];
        $groupIndex = 1;

        foreach ($groups as $groupValues) {
            if (!empty($groupValues)) {
                foreach ($groupValues as $position => $value) {
                    $items[] = [
                        'group' => $groupIndex,
                        'position' => (int) $position + 1, // 从1开始计数
                        'value' => $value,
                    ];
                }
            }
            ++$groupIndex;
        }

        if (count($items) < $combinationSize) {
            return [];
        }

        return self::generateCombinations($items, $combinationSize);
    }

    /**
     * 使用迭代方法生成组合，避免递归开销
     *
     * @param array $items
     * @param int $combinationSize
     * @return array
     */
    private static function generateCombinations(array $items, int $combinationSize): array
    {
        $totalItems = count($items);
        $result = [];

        // 初始化索引数组
        $indices = range(0, $combinationSize - 1);

        do {
            // 构建当前组合
            $keyParts = [];
            $values = [];

            foreach ($indices as $index) {
                $item = $items[$index];
                $keyParts[] = $item['group'] . '_' . $item['position'];
                $values[] = $item['value'];
            }

            $result[implode('|', $keyParts)] = $values;
        } while (self::nextCombination($indices, $totalItems, $combinationSize));

        return $result;
    }

    /**
     * 计算下一个组合的索引.
     *
     * @param array $indices
     * @param int $totalItems
     * @param int $combinationSize
     * @return bool
     */
    private static function nextCombination(array &$indices, int $totalItems, int $combinationSize): bool
    {
        for ($i = $combinationSize - 1; $i >= 0; --$i) {
            if ($indices[$i] < $totalItems - ($combinationSize - $i)) {
                ++$indices[$i];

                // 重置后续索引
                for ($j = $i + 1; $j < $combinationSize; ++$j) {
                    $indices[$j] = $indices[$j - 1] + 1;
                }

                return true;
            }
        }

        return false;
    }
}
