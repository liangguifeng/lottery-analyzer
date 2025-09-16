<?php

namespace Liangguifeng\LotteryAnalyzer\Analyzer;

class DanmaAnalyzer
{
    protected array $historyData;

    protected array $analyzerData;

    public function __construct(array $historyData)
    {
        $this->historyData = $historyData;
    }

    public function get($periods = 3, $minConsecutive = 3)
    {
        // 预测数据
        $predictData= array_slice($this->historyData, 0, $periods, true);

        // 分析数据
        $this->analyzerData = array_slice($this->historyData, $periods, -1, true);

        // 间隔期数拆分
        $chuckAnalyzerData = $this->arrayChuck($this->analyzerData, $periods + 1);

        // 最小连续期数
        $chunks = array_slice($chuckAnalyzerData, 0, $minConsecutive);

        $hitList = [];
        foreach ($chunks as $chunk) {
            $chunk = array_reverse($chunk, true);
            // 规律期数
            $checkChuckData = array_slice($chunk, 0, $periods, true);
            // 规律期数 - 排列组合
            $checkData = $this->generateCombinationsAcrossGroups($checkChuckData);

            // 预测
            $nextData = array_slice($chunk, $periods, $periods + 1, true);

            // 检查规律期数的排列组合在预测期数内出现的数据
            $data = $this->check($checkData, reset($nextData));
            if (!empty($data)) {
                $hitList[] = $data;
            }
        }

        $result = [];
        foreach ($hitList[0] as $key => $value) {
            $existsInAll = true;

            for ($i = 1; $i < count($hitList); $i++) {
                if (!array_key_exists($key, $hitList[$i])) {
                    $existsInAll = false;
                    break;
                }
            }

            if ($existsInAll) {
                $result[] = $key;
            }
        }

        return $result;
    }

    private function check($checkData, $nextData)
    {
        $result = [];
        foreach ($checkData as $period => $checkList) {
            foreach ($checkList as $value) {
                if (in_array($value, $nextData)) {
                    $result[$period] = implode('|', $checkList);
                }
            }
        }

        return $result;
    }

    private function arrayChuck(array $array, int $size): array
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

    private function generateCombinationsAcrossGroups(array $source, int $m = 3): array
    {
        $items = [];
        $groupIndex = 1;
        foreach ($source as $period => $vals) {
            foreach ($vals as $i => $v) {
                $items[] = [
                    'period' => $period,
                    'group' => $groupIndex,
                    'index' => $i + 1,
                    'value' => $v
                ];
            }
            $groupIndex++;
        }

        $n = count($items);
        $result = [];
        if ($m <= 0 || $m > $n) {
            return $result;
        }
        if ($m === 3) {
            for ($a = 0; $a < $n - 2; $a++) {
                for ($b = $a + 1; $b < $n - 1; $b++) {
                    for ($c = $b + 1; $c < $n; $c++) {
                        $k = $items[$a]['group'] . '_' . $items[$a]['index']
                            . '|' .  $items[$b]['group'] . '_' . $items[$b]['index']
                            . '|' .  $items[$c]['group'] . '_' . $items[$c]['index'];

                        $result[$k] = [
                            $items[$a]['value'],
                            $items[$b]['value'],
                            $items[$c]['value']
                        ];
                    }
                }
            }
            return $result;
        }

        $stack = [];
        $generate = function ($start, $depth) use (&$generate, $items, &$result, &$stack, $m, $n) {
            if ($depth === $m) {
                $parts = [];
                $vals = [];
                foreach ($stack as $idx) {
                    $it = $items[$idx];
                    $parts[] = $it['group'] . '_' . $it['index'];
                    $vals[] = $it['value'];
                }
                $result[implode('|', $parts)] = $vals;
                return;
            }

            for ($i = $start; $i <= $n - ($m - $depth); $i++) {
                $stack[$depth] = $i;
                $generate($i + 1, $depth + 1);
            }
        };

        $generate(0, 0);
        return $result;
    }
}