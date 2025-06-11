<?php

declare(strict_types=1);

namespace Smspro\Test\TestCase;

use Smspro\Http\Curl\Domain\Client\ClientInterface;
use Smspro\Http\Curl\Domain\Response\ResponseInterface;
use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Http\Command\ExecuteRequestCommandHandler;
use Smspro\Sms\Http\Response;
use Smspro\Sms\TopUp;
use PHPUnit\Framework\TestCase;

class TopUpTest extends TestCase
{
    public function testCanAdd(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $httpClient = $this->createMock(ClientInterface::class);
        $handler = new ExecuteRequestCommandHandler(null, $httpClient);
        /** @var TopUp|\Smspro\Sms\Objects\TopUp $topUp */
        $topUp = TopUp::create('key', 'secret', $handler);
        $return = json_encode([
            'status' => 'OK',
            'message' => 'pending',
            'topup' => [
                'id' => '04186609-3bda-4f30-9aaf-e4638b00d5c2',
                'amount' => 3000,
                'currency' => 'XAF',
                'status' => 'PENDING',
                'network' => 'orange',
            ],
            'code' => 200,
        ]);
        $topUp->phonenumber = '675234590';
        $topUp->amount = 3000;
        $response->expects($this->exactly(2))->method('getStatusCode')->willReturn(200);
        $response->expects($this->once())->method('getJson')->will($this->returnValue(json_decode($return, true)));
        $httpClient->expects($this->once())->method('sendRequest')
            ->will($this->returnValue($response));

        $result = $topUp->add();

        $this->assertEquals(3000, $result->getAmount());
        $this->assertSame('XAF', $result->getCurrency());
        $this->assertSame('PENDING', $result->getStatus());
        $this->assertSame('orange', $result->getNetwork());
        $this->assertSame('04186609-3bda-4f30-9aaf-e4638b00d5c2', $result->getId());
        $this->assertInstanceOf(Response::class, $result->getResponse());
    }

    public function testAddFailure(): void
    {
        $this->expectException(SmsproException::class);
        $httpClient = $this->createMock(ClientInterface::class);
        $handler = new ExecuteRequestCommandHandler(null, $httpClient);
        /** @var TopUp|\Smspro\Sms\Objects\TopUp $topUp */
        $topUp = TopUp::create('key', 'secret', $handler);

        $topUp->add();
    }
}
