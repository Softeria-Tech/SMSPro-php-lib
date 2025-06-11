<?php

declare(strict_types=1);

namespace Smspro\Test\TestCase;

use Smspro\Http\Curl\Domain\Client\ClientInterface;
use Smspro\Http\Curl\Domain\Response\ResponseInterface;
use Smspro\Sms\Balance;
use Smspro\Sms\Entity\Credential;
use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Http\Command\ExecuteRequestCommand;
use Smspro\Sms\Http\Command\ExecuteRequestCommandHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class BalanceTest
 *
 * @author Softeria Tech
 *
 * @covers \Smspro\Sms\Balance
 */
class BalanceTest extends TestCase
{
    /** @covers \Smspro\Sms\Balance::get */
    public function testGetSuccess(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $httpClient = $this->createMock(ClientInterface::class);
        $handler = new ExecuteRequestCommandHandler(null, $httpClient);
        /** @var Balance|\Smspro\Sms\Objects\Balance $balance */
        $balance = Balance::create('key', 'secret', $handler);
        $return = json_encode([
            '_message' => 'succes',
            'balance' => [
                'balance' => 220,
                'currency' => 'XAF',
            ],
            'code' => 200,
        ]);

        $response->expects($this->exactly(2))->method('getStatusCode')->willReturn(200);
        $response->expects($this->once())->method('getJson')->willReturn(json_decode($return, true));
        $httpClient->expects($this->once())->method('sendRequest')
            ->will($this->returnValue($response));
        $result = $balance->get();
        $this->assertEquals(220, $result->getBalance());
        $this->assertSame('XAF', $result->getCurrency());
    }

    /** @covers \Smspro\Sms\Balance::get */
    public function testGetFailure(): void
    {
        $this->expectException(SmsproException::class);
        $handler = $this->createMock(ExecuteRequestCommandHandler::class);
        /** @var Balance|\Smspro\Sms\Objects\Balance $balance */
        $balance = Balance::create('key', 'secret', $handler);
        $command = new ExecuteRequestCommand(
            'GET',
            'https://sms.softeriatech.com/api/v1/sms/balance.json',
            [],
            new Credential('key', 'secret')
        );
        $handler->expects($this->once())->method('handle')->with($command)
            ->will($this->returnValue(new SmsproException('Test')));
        $balance->get();
    }
}
