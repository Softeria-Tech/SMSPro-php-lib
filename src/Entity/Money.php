<?php

declare(strict_types=1);

namespace Smspro\Sms\Entity;

final class Money
{
    public function __construct(public readonly float $value, public readonly string $currency)
    {
    }
}
