<?php

declare(strict_types=1);

namespace Smspro\Sms\Interfaces;

interface OperatingSystemInterface
{
    public function get(): string;
}
