<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Support;

use Liangguifeng\LotteryAnalyzer\Exceptions\InvalidDataException;

class RateHelper
{
    public static function getRateToArray(string $rate)
    {
        $arr = explode(':', $rate);
        if (count($arr) != 2) {
            throw new InvalidDataException('Rate must be in format of "x:y"', 21010);
        }
        return $arr;
    }
}
