<?php

declare(strict_types=1);

namespace Smspro\Sms\Console;

use Smspro\Sms\Exception\BackgroundProcessException;
use Smspro\Sms\Interfaces\OperatingSystemInterface;

class BackgroundProcess
{
    private const ALLOWED_OS = ['LINUX', 'FREEBSD', 'DARWIN'];

    public function __construct(
        private ?string $command = null,
        private readonly ?OperatingSystemInterface $os = null
    ) {
    }

    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function run(string $outputFile = '/dev/null', bool $append = false): int
    {
        $command = $this->getCommand();

        if ($command === null) {
            throw new BackgroundProcessException('Command is missing');
        }

        $osName = ($this->os ?? new OperatingSystem())->get();

        if (empty($osName)) {
            throw new BackgroundProcessException('Operating System cannot be determined');
        }

        // Windows handling
        if (str_starts_with($osName, 'WIN')) {
            shell_exec(sprintf('%s &', $command));

            return 0;
        }

        // Check for supported OS
        if (!in_array($osName, self::ALLOWED_OS, true)) {
            throw new BackgroundProcessException(sprintf('Operating System "%s" not Supported', $osName));
        }

        // Other OS handling
        $redirectType = $append ? '>>' : '>';

        return (int)shell_exec(
            sprintf(
                '%s %s %s 2>&1 & echo $!',
                $command,
                $redirectType,
                $outputFile
            )
        );
    }

    public function canBackground(): bool
    {
        return function_exists('shell_exec');
    }

    private function getCommand(): ?string
    {
        return $this->command ? escapeshellcmd($this->command) : null;
    }
}
