<?php

namespace Smspro\Test\TestCase;

use GuzzleHttp\Client;
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

    public function testApiReachable(): void
    {
        $client = new Client();
        $results=$client->get(Constants::END_POINT_URL."/v1", [
            'http_errors' => false,
        ]);
        $this->assertTrue($results->getStatusCode() < 400, 'API is not reachable. Status code: ' . $results->getStatusCode());
    }
}
