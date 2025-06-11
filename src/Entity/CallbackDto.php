<?php

declare(strict_types=1);

namespace Smspro\Sms\Entity;

use Smspro\Sms\Interfaces\DriversInterface;

final class CallbackDto
{
    public function __construct(
        public readonly ?DriversInterface $driver = null,
        public readonly ?DbConfig $dbConfig = null,
        public readonly ?TableMapping $tableMapping = null,
        public readonly ?int $bulkChunkLimit = null,
    ) {
    }
}
