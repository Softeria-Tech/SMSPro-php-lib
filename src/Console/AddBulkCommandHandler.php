<?php

declare(strict_types=1);

namespace Smspro\Sms\Console;

use Smspro\Sms\Constants;
use Smspro\Sms\Exception\BackgroundProcessException;
use Smspro\Sms\Lib\Utils;
use Exception;

final class AddBulkCommandHandler
{
    private const PHP_BIN = 'php';

    public function __construct(private readonly ?BackgroundProcess $backgroundProcess = null)
    {
    }

    /**
     * Handles the addition of bulk commands.
     *
     * @throws Exception
     */
    public function handle(AddBulkCommand $command): int
    {
        $temporaryFileName = Utils::randomStr() . '.bulk';
        $binaryPath = $command->binPath ?? self::PHP_BIN;

        if ($binaryPath !== 'php' && !is_executable($binaryPath)) {
            return 0;
        }

        $temporaryFilePath = Constants::getSMSPath() . 'tmp/' . $temporaryFileName;
        $fileWriteSuccess = file_put_contents(
            $temporaryFilePath,
            json_encode($command->data) . PHP_EOL,
            LOCK_EX
        );

        if ($fileWriteSuccess === false) {
            return 0;
        }

        return $this->add($command, $binaryPath, $temporaryFileName);
    }

    private function add(AddBulkCommand $command, string $binaryPath, string $temporaryFileName): int
    {
        $commandBase = $binaryPath . ' -f ' . Constants::getSMSPath() . 'bin/smspro.php';
        $serializedData = serialize([
            $command->callback,
            $temporaryFileName,
            ['api_key' => $command->credentials->key, 'api_secret' => $command->credentials->secret],
        ]);

        $process = $this->backgroundProcess ?? new BackgroundProcess();

        if (!$process->canBackground()) {
            throw new BackgroundProcessException('Function "shell_exec" is required for background processing.');
        }

        $fullCommand = $commandBase . ' ' . base64_encode($serializedData);

        return $process->setCommand($fullCommand)->run();
    }
}
