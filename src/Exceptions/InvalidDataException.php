<?php

declare(strict_types=1);

namespace Liangguifeng\LotteryAnalyzer\Exceptions;

use InvalidArgumentException;

class InvalidDataException extends InvalidArgumentException
{
    /**
     * 构造函数.
     *
     * @param string $message 异常信息
     * @param int $code 异常码
     */
    public function __construct(string $message = 'Invalid lottery data.', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
