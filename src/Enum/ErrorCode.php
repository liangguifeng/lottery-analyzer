<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Enum;

/**
 * 异常错误码定义.
 *
 * @property $value
 */
enum ErrorCode: int
{
    case EMPTY_HISTORY = 1001;         // 历史数据为空
    case INVALID_RATE = 1002;      // 无效的比例
    case UNKNOWN_ERROR = 1099;         // 未知错误（兜底）
}
