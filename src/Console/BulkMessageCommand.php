<?php

declare(strict_types=1);

namespace Smspro\Sms\Console;

use Smspro\Sms\Entity\CallbackDto;

final class BulkMessageCommand
{
    public function __construct(
        public readonly array $data,
        public readonly ?CallbackDto $callback = null
    ) {
    }
}
