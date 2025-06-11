<?php

declare(strict_types=1);

namespace Smspro\Test\TestCase\Console;

use Smspro\Sms\Console\AddBulkCommand;
use Smspro\Sms\Console\AddBulkCommandHandler;
use Smspro\Sms\Console\BackgroundProcess;
use Smspro\Sms\Entity\Credential;
use Smspro\Sms\Exception\BackgroundProcessException;
use Exception;
use PHPUnit\Framework\TestCase;

class AddBulkCommandHandlerTest extends TestCase
{
    /** @throws Exception */
    public function testCannotExecute(): void
    {
        $handler = new AddBulkCommandHandler();
        $command = new AddBulkCommand(
            new Credential('key', 'secret'),
            [],
            [],
            ''
        );
        $this->assertEquals(0, $handler->handle($command));
    }

    /** @throws Exception */
    public function testCanHandle(): void
    {
        $process = $this->createMock(BackgroundProcess::class);
        $process->expects($this->once())->method('canBackground')->will($this->returnValue(true));
        $process->expects($this->once())->method('setCommand')->willReturn($process);
        $process->expects($this->once())->method('run')->will($this->returnValue(1));
        $handler = new AddBulkCommandHandler($process);
        $command = new AddBulkCommand(
            new Credential('key', 'secret'),
            [],
            [],
        );

        $this->assertEquals(1, $handler->handle($command));
    }

    public function testThrowsException(): void
    {
        $this->expectException(BackgroundProcessException::class);
        $this->expectExceptionMessage('Function "shell_exec" is required for background processing');
        $process = $this->createMock(BackgroundProcess::class);
        $process->expects($this->once())->method('canBackground')->will($this->returnValue(false));
        $command = new AddBulkCommand(
            new Credential('key', 'secret'),
            [],
            [],
        );

        (new AddBulkCommandHandler($process))->handle($command);
    }
}
