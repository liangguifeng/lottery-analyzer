<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Enum;

enum DigitLength: int
{
    case THREE = 3;  // 3位数
    case FOUR = 4;   // 4位数
    case SEVEN = 7;  // 7位数
}
