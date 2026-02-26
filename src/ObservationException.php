<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Exception;
use Throwable;
use Verdient\Hyperf3\Utils\VarDumper;

/**
 * 观测异常
 *
 * @author Verdient。
 */
class ObservationException extends Exception
{
    /**
     * 调用的函数
     *
     * @param string $message 消息
     * @param ?array $args 参数
     * @param ?Throwable $previous
     *
     * @author Verdient。
     */
    public function __construct(string $message = "", ?array $args = null, ?Throwable $previous = null)
    {
        if (!empty($args)) {
            $message .= "\n" . VarDumper::dumpAsString($args);
        }

        parent::__construct($message, $previous ? $previous->getCode() : 0, $previous);
    }
}
