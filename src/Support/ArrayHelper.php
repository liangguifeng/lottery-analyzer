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
        // 将原始数组扁平化为带组号和元素索引的项
        $flattenedItems = [];
        $groupNumber = 1;
        foreach ($groups as $groupValues) {
            foreach ($groupValues as $index => $value) {
                $flattenedItems[] = [
                    'group' => $groupNumber,
                    'position' => (int) $index + 1, // 从1开始计数
                    'value' => $value,
                ];
            }
            ++$groupNumber;
        }

        $totalItems = count($flattenedItems);
        $result = [];
        $stack = [];

        // 递归生成组合
        $recurse = function (int $startIndex, int $depth) use (&$recurse, &$result, &$stack, $flattenedItems, $totalItems, $combinationSize) {
            if ($depth === $combinationSize) {
                $keyParts = [];
                $values = [];
                foreach ($stack as $itemIndex) {
                    $item = $flattenedItems[$itemIndex];
                    $keyParts[] = $item['group'] . '_' . $item['position'];
                    $values[] = $item['value'];
                }
                $result[implode('|', $keyParts)] = $values;
                return;
            }

            // 从当前索引开始生成组合
            for ($i = $startIndex; $i <= $totalItems - ($combinationSize - $depth); ++$i) {
                $stack[$depth] = $i;
                $recurse($i + 1, $depth + 1);
            }
        };

        $recurse(0, 0);

        return $result;
    }
}
