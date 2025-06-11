<?php

declare(strict_types=1);

namespace Smspro\Sms\Console;

use Smspro\Sms\Interfaces\OperatingSystemInterface;

final class OperatingSystem implements OperatingSystemInterface
{
    public function get(): string
    {
        return strtoupper(PHP_OS ?: '');
    }
}
