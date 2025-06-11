<?php

declare(strict_types=1);

namespace Smspro\Sms\Database\Repository;

use Smspro\Sms\Database\MySQL;
use Smspro\Sms\Entity\DbConfig;
use Smspro\Sms\Interfaces\DriversInterface;
use Smspro\Sms\Interfaces\LogRepositoryInterface;

final class LogRepository implements LogRepositoryInterface
{
    public function __construct(private readonly DbConfig $dbConfig, private readonly ?DriversInterface $driver = null)
    {
    }

    public function save(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $table = $this->dbConfig->tableName;
        if (empty($table)) {
            return false;
        }
        $prefix = $this->dbConfig->prefix ?? '';
        $dbTable = $prefix . $table;
        $driver = $this->driver ?? MySQL::getInstance($this->dbConfig);

        return (bool)$driver->getDB()?->insert($dbTable, $data);
    }
}
