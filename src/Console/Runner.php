<?php

declare(strict_types=1);

namespace Smspro\Sms\Console;

use Smspro\Sms\Constants;
use Smspro\Sms\Database\MySQL;
use Smspro\Sms\Entity\CallbackDto;
use Smspro\Sms\Entity\Credential;
use Smspro\Sms\Entity\DbConfig;
use Smspro\Sms\Entity\TableMapping;
use Smspro\Sms\Interfaces\DriversInterface;
use Smspro\Sms\Lib\Utils;
use Smspro\Sms\Message;
use Throwable;

class Runner
{
    public function __construct(private readonly ?BulkMessageCommandHandler $bulkMessageCommandHandler = null)
    {
    }

    public function run(array $argv): void
    {
        $commandArg = $argv[1] ?? null;
        if (!$commandArg) {
            return;
        }

        $arguments = $this->unSerializeArgument(base64_decode($commandArg, true) ?: '');

        if (!$arguments || count($arguments) < 3) {
            return;
        }

        [$callback, $sTmpName, $credentials] = $arguments;
        $tmpFilePath = Constants::getSMSPath() . 'tmp/' . $sTmpName;

        if (is_file($tmpFilePath) && is_readable($tmpFilePath)) {
            $tmpContent = file_get_contents($tmpFilePath);

            if ($tmpContent && ($bulkData = Utils::decodeJson($tmpContent, true))) {
                unlink($tmpFilePath);
                $this->applyBulk($credentials, $bulkData, $this->ensureCallback($callback));
            }
        }
    }

    private function unSerializeArgument(string $argument): ?array
    {
        $allowedClasses = [
            TableMapping::class,
            DbConfig::class,
            MySQL::class,
            DriversInterface::class,
            CallbackDto::class,
        ];
        try {
            return unserialize($argument, ['allowed_classes' => $allowedClasses]);
        } catch (Throwable) {
            return null;
        }
    }

    private function ensureCallback(CallbackDto|array $callback): CallbackDto
    {
        if ($callback instanceof CallbackDto) {
            return $callback;
        }

        return new CallbackDto(
            $this->assertDriver($callback['driver'] ?? null),
            $this->assertDbConfig($callback['db_config'] ?? null),
            $this->assertTableMapping($callback['table_mapping'] ?? null),
            $callback['bulk_chunk'] ?? null
        );
    }

    private function applyBulk(array $credentials, array $bulkData, CallbackDto $callback): void
    {
        $credentialObj = new Credential($credentials['pro_api_key'], $credentials['api_secret']);
        $command = new BulkMessageCommand($bulkData, $callback);

        $handler = $this->bulkMessageCommandHandler ?? new BulkMessageCommandHandler(
            Message::create($credentialObj->key, $credentialObj->secret)
        );

        foreach ($handler->handle($command) as $message) {
            if ($message->getId()) {
                echo 'MessageId is: ' . $message->getId() . PHP_EOL;
            }
        }
    }

    private function assertDriver(DriversInterface|array|null $driver): ?DriversInterface
    {
        return is_array($driver) ? call_user_func($driver) : $driver;
    }

    private function assertDbConfig(DbConfig|array|null $dbConfig): ?DbConfig
    {
        if (is_array($dbConfig)) {
            return new DbConfig(
                $dbConfig['db_name'],
                $dbConfig['db_user'],
                $dbConfig['db_password'],
                $dbConfig['table_sms'],
                $dbConfig['table_prefix'] ?? null,
                $dbConfig['db_host'] ?? null
            );
        }

        return $dbConfig;
    }

    private function assertTableMapping(TableMapping|array|null $tableMapping): ?TableMapping
    {
        if (is_array($tableMapping)) {
            return new TableMapping(
                $tableMapping['message'],
                $tableMapping['recipient'],
                $tableMapping['message_id'],
                $tableMapping['sender'],
                $tableMapping['response']
            );
        }

        return $tableMapping;
    }
}
