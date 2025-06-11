<?php

declare(strict_types=1);

namespace Smspro\Sms\Console;

final class EncryptCommand
{
    public function __construct(public readonly string $publicKeyFile, public readonly string $message)
    {
    }
}
