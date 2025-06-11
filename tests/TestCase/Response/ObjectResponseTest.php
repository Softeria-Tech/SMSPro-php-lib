<?php

declare(strict_types=1);

namespace Smspro\Test\TestCase\Response;

use Smspro\Http\Curl\Domain\Response\ResponseInterface;
use Smspro\Sms\Http\Response;
use Smspro\Sms\Response\ObjectResponse;
use PHPUnit\Framework\TestCase;

class ObjectResponseTest extends TestCase
{
    public function testCanCreateInstance(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $json = file_get_contents(dirname(__DIR__, 2) . '/Fixture/balance.json');
        $responseMock->expects($this->once())->method('getStatusCode')->willReturn(200);
        $responseMock->expects($this->once())->method('getJson')->willReturn(json_decode($json, true));
        $response = new Response($responseMock);
        $response1 = new ObjectResponse($response);
        $this->assertInstanceOf(ObjectResponse::class, $response1);
    }
}
