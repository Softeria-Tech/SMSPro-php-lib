<?php

declare(strict_types=1);

namespace Smspro\Sms\Database;

use Smspro\Sms\Entity\DbConfig;
use Smspro\Sms\Interfaces\DriversInterface;
use Exception;
use mysqli;
use mysqli_result;

/**
 * Class MySQL
 * Represents the MySQL driver for database interactions.
 */
class MySQL implements DriversInterface
{
    private const DEFAULT_DB_PORT = 3306;

    private ?mysqli $connection = null;

    public function __construct(private readonly ?DbConfig $dbConfig = null, private readonly ?mysqli $mysqli = null)
    {
    }

    public function __destruct()
    {
        $this->close();
    }

    public static function getInstance(?DbConfig $dbConfig = null): self
    {
        return new self($dbConfig);
    }

    /** Get database connection. */
    public function getDB(): ?self
    {
        $this->connection = $this->dbConnect($this->dbConfig);

        return $this->connection ? $this : null;
    }

    /** Get database configuration. */
    public function getDbConfig(): ?DbConfig
    {
        return $this->dbConfig;
    }

    /** Escape given string for safe SQL execution. */
    public function escapeString(string $string): string
    {
        return $this->connection->escape_string(trim($string));
    }

    /** Close database connection. */
    public function close(): bool
    {
        return (bool)$this->connection?->close();
    }

    /** Execute SQL query. */
    public function query(string $query): ?mysqli_result
    {
        $result = $this->connection->query($query);

        return $result ?: null;
    }

    /** Insert a record into the specified table. */
    public function insert(string $table, array $variables = []): bool
    {
        if (empty($variables)) {
            return false;
        }

        $sql = 'INSERT INTO ' . $this->escapeString($table);
        $fields = array_keys($variables);
        $values = array_map(fn (mixed $value) => "'" . $this->escapeString($value) . "'", $variables);

        $sql .= ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
        $query = $this->query($sql);

        return (bool)$query;
    }

    /** Retrieve the last error encountered. */
    public function getError(): string
    {
        return $this->connection?->error ?? '';
    }

    /** Establish a database connection. */
    protected function dbConnect(?DbConfig $dbConfig): ?mysqli
    {
        try {
            $mysqlConnection = $this->mysqli ?? new mysqli(
                $dbConfig->host ?? null,
                $dbConfig->dbUser ?? null,
                $dbConfig->password ?? null,
                $dbConfig->dbName ?? null,
                $dbConfig->dbPort ?? self::DEFAULT_DB_PORT
            );
        } catch (Exception $exception) {
            $this->handleDbConnectionError($exception);

            return null;
        }

        return $mysqlConnection;
    }

    /** Handle database connection errors. */
    private function handleDbConnectionError(Exception $exception): void
    {
        echo 'Failed to connect to MySQL: ' . $exception->getMessage() . "\n";
    }
}
