<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Exceptions;

use InvalidArgumentException;
use Liangguifeng\LotteryAnalyzer\Enum\ErrorCode;

class InvalidDataException extends InvalidArgumentException
{
    /**
     * 构造函数.
     *
     * @param string $message 异常信息
     * @param int $code 异常码
     */
    public function __construct(string $message = 'Invalid lottery data.', ErrorCode $code = ErrorCode::UNKNOWN_ERROR)
    {
        parent::__construct($message, $code->value);
    }
}
