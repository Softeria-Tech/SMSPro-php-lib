<?php

declare(strict_types=1);

namespace Smspro\Sms\Exception;

use RuntimeException;
use Throwable;

/**
 * Class SmsproException
 */
class SmsproException extends RuntimeException
{
    public const ERROR_CODE = 500;

    /** Json encodes the message and calls the parent constructor. */
    public function __construct(object|array|string|null $message = null, int $code = 0, ?Throwable $previous = null)
    {
        if ($code === 0) {
            $code = self::ERROR_CODE;
        }
        $exceptionMessage = is_array($message) || is_object($message) ? json_encode($message) : $message;
        parent::__construct($exceptionMessage, $code, $previous);
    }
}
