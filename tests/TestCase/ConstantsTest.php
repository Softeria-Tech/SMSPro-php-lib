<?php

namespace Smspro\Test\TestCase;

use Smspro\Sms\Constants;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConstantsTest
 *
 * @author Softeria Tech
 *
 * @covers \Smspro\Sms\Constants
 */
class ConstantsTest extends TestCase
{
    public function testGetPhpVersion(): void
    {
        $this->assertStringContainsString('PHP/', Constants::getPhpVersion());
    }

    public function testGetPhpVersionThrowsError(): void
    {
        $this->expectException(LogicException::class);
        $constants = new class () extends Constants {
            public const MIN_PHP_VERSION = 9999999999999999999999999999;
        };

        $constants::getPhpVersion();
    }

    public function testGetSMSPath(): void
    {
        $this->assertSame(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR, Constants::getSMSPath());
    }
}
