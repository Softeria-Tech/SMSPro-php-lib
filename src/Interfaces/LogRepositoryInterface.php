<?php

declare(strict_types=1);

namespace Smspro\Sms\Interfaces;

interface LogRepositoryInterface
{
    public function save(array $data): bool;
}
