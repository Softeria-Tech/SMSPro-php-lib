<?php

namespace Smspro\Test\TestCase;

use Smspro\Http\Curl\Domain\Response\ResponseInterface;
use Smspro\Sms\Base;
use Smspro\Sms\Entity\Credential;
use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Http\Client as HttpClient;
use Smspro\Sms\Http\Command\ExecuteRequestCommand;
use Smspro\Sms\Http\Command\ExecuteRequestCommandHandler;
use Smspro\Sms\Http\Response;
use Smspro\Sms\Message;
use Smspro\Sms\Objects;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseTest
 *
 * @author Softeria Tech
 *
 * @covers \Smspro\Sms\Base
 */
class BaseTest extends TestCase
{
    private $oBase;

    private ?Response $clientResponse;

    public function setUp(): void
    {
        $this->oBase = new Base();
        $this->clientResponse = $this->createMock(Response::class);
    }

    public function tearDown(): void
    {
        if (file_exists(dirname(dirname(__DIR__)) . '/config/app.php')) {
            @unlink(dirname(dirname(__DIR__)) . '/config/app.php');
        }
        if (file_exists('/tmp/test.pem')) {
            @unlink('/tmp/test.pem');
        }
        if (file_exists('/tmp/test2.pem')) {
            @unlink('/tmp/test.pem');
        }

        unset($this->oBase);
    }

    /**
     * @covers \Smspro\Sms\Base::setResourceName
     *
     * @dataProvider resourceDataProvider
     */
    public function testSetResource(string $data): void
    {
        $this->assertNull($this->oBase->setResourceName($data));
    }

    /**
     * @covers \Smspro\Sms\Base::getResourceName
     *
     * @dataProvider resourceDataProvider
     */
    public function testGetResource(string $data): void
    {
        $this->assertNull($this->oBase->setResourceName($data));
        $this->assertEquals($this->oBase->getResourceName(), $data);
    }

    /**
     * @covers \Smspro\Sms\Base::create
     *
     * @dataProvider createDataProvider
     */
    public function testCreate(string $apikey, string $apisecret): void
    {
        $this->assertInstanceOf(Base::class, Base::create($apikey, $apisecret));
    }

    /**
     * @covers \Smspro\Sms\Base::clear
     *
     * @dataProvider createDataProvider
     */
    public function testCreateObj(string $apikey, string $apisecret): void
    {
        $this->assertNull($this->oBase->clear());
        $this->assertIsObject(Message::create($apikey, $apisecret));
    }

    /**
     * @covers \Smspro\Sms\Base::getDataObject
     *
     * @dataProvider createDataProvider
     *
     * @depends testCreateObj
     */
    public function testgetDataObject(string $apikey, string $apisecret)
    {
        $this->assertInstanceOf(Objects\Message::class, $this->oBase->getDataObject());
    }

    /**
     * @covers \Smspro\Sms\Base::getConfigs
     *
     * @dataProvider createDataProvider
     *
     * @depends testCreateObj
     */
    public function testgetConfigs($apikey, $apisecret)
    {
        $this->assertIsArray($this->oBase->getConfigs());
    }

    /**
     * @covers \Smspro\Sms\Base::getCredentials
     *
     * @dataProvider createDataProvider
     *
     * @depends testCreateObj
     */
    public function testgetCredentials($apikey, $apisecret)
    {
        $this->assertIsArray($this->oBase->getCredentials());
    }

    /**
     * @covers \Smspro\Sms\Base::getData
     *
     * @dataProvider createDataProvider
     *
     * @depends testCreateObj
     */
    public function testgetData($apikey, $apisecret)
    {
        $this->assertNull($this->oBase->clear());
        $oMessage = Message::create($apikey, $apisecret);
        $oMessage->from = 'YourCompany';
        $oMessage->to = '+237612345678';
        $oMessage->message = 'Hello Kmer World! Déjà vu!';
        $this->assertIsArray($this->oBase->getData());
    }

    /**
     * @covers \Smspro\Sms\Base::getData
     *
     * @dataProvider createDataProvider
     *
     * @depends testCreateObj
     */
    public function testgetDataException($apikey, $apisecret)
    {
        $oMessage = Message::create($apikey, $apisecret);
        $oMessage->from = 'YourCompany';
        $oMessage->tel = '+23712345678';
        $oMessage->message = 'Hello Kmer World! Déjà vu!';
        $this->assertIsArray($oMessage->getData());
        $this->assertEquals([], $oMessage->getData());
    }

    /**
     * @covers \Smspro\Sms\Base::getData
     *
     * @dataProvider createDataProvider
     *
     * @depends testCreateObj
     */
    public function testgetDataGet($apikey, $apisecret)
    {
        //$this->expectException(SmsproException::class);
        $oMessage = Message::create($apikey, $apisecret);
        $oMessage->from = 'YourCompany';
        $oMessage->to = '+237612345678';
        $oMessage->message = 'Hello Kmer World! Déjà vu!';
        $this->assertEquals($this->equalTo($oMessage->from), $this->equalTo('YourCompany'));
    }

    /**
     * @covers \Smspro\Sms\Base::getEndPointUrl
     *
     * @dataProvider resourceDataProvider
     */
    public function testgetEndPointUrl($data)
    {
        $this->oBase->setResourceName($data);
        $this->assertStringContainsString('sms.softeriatech.com/api', $this->oBase->getEndPointUrl());
    }

    /**
     * @covers \Smspro\Sms\Base::setResponseFormat
     *
     * @dataProvider formatDataProvider
     */
    public function testsetResponseFormat($data)
    {
        $this->assertNull($this->oBase->setResponseFormat($data));
    }

    /**
     * @covers \Smspro\Sms\Base::execRequest
     *
     * @dataProvider createDataProvider
     *
     * @depends testCreateObj
     */
    public function testExecRequest(string $apikey, string $apisecret): void
    {
        $this->assertNull($this->oBase->clear());
        $handler = $this->createMock(ExecuteRequestCommandHandler::class);
        /** @var Message|Objects\Message|Base $oMessage */
        $oMessage = Message::create($apikey, $apisecret, $handler);
        $oMessage->from = 'YourCompany';
        $oMessage->to = '+237612345678';
        $oMessage->message = 'Hello Kmer World! Déjà vu!';

        $command = new ExecuteRequestCommand('POST', 'https://sms.softeriatech.com/api/v1/sms.json', [
            'from' => 'YourCompany',
            'message' => 'Hello Kmer World! Déjà vu!',
            'to' => ['+237612345678'],
        ], new Credential($apikey, $apisecret));
        $handler->expects($this->any())->method('handle')->with($command)
            ->will($this->returnValue($this->clientResponse));

        $oMessage->execRequest(HttpClient::POST_REQUEST);
    }

    /**
     * @covers \Smspro\Sms\Base::execRequest
     *
     * @dataProvider createDataProvider
     */
    public function testExecRequestEnc(string $apikey, string $apisecret): void
    {
        $this->assertNull($this->oBase->clear());
        $handler = $this->createMock(ExecuteRequestCommandHandler::class);
        $oMessage = Message::create($apikey, $apisecret, $handler);
        $oMessage->from = 'YourCompany';
        $oMessage->to = '+237612345678';
        $oMessage->encrypt = true;
        $oMessage->message = 'Hello Kmer World! Déjà vu!';

        $handler->expects($this->any())->method('handle')->with(
            $this->callback(function (ExecuteRequestCommand $command) use ($apikey, $apisecret) {
                $this->assertStringStartsWith('-----BEGIN PGP MESSAGE', $command->data['message']);
                $this->assertSame($apikey, $command->credential->key);
                $this->assertSame($apisecret, $command->credential->secret);

                return true;
            })
        )
            ->will($this->returnValue($this->clientResponse));

        $oMessage->execRequest(HttpClient::POST_REQUEST, true, null);
    }

    /**
     * @covers \Smspro\Sms\Base::execRequest
     *
     * @dataProvider createDataProvider
     */
    public function testexecRequestException1($apikey, $apisecret)
    {
        $this->expectException(SmsproException::class);
        $oMessage = Message::create($apikey, $apisecret);
        $oMessage->from = 'YourCompany';
        $oMessage->tel = '+237612345678';
        $oMessage->message = 'Hello Kmer World! Déjà vu!';

        $oMessage->execRequest(HttpClient::POST_REQUEST);
    }

    /**
     * @covers \Smspro\Sms\Base::execRequest
     *
     * @dataProvider createDataProvider
     */
    public function testexecRequestException2($apikey, $apisecret)
    {
        $this->expectException(SmsproException::class);
        $handler = $this->createMock(ExecuteRequestCommandHandler::class);
        $this->oBase->clear();
        $oMessage = Message::create($apikey . 'epi2', $apisecret . 'epi2', $handler);
        $oMessage->from = 'YourCompany';
        $oMessage->to = '+237612345678';
        $oMessage->message = 'Hello Kmer World! Déjà vu!';

        $command = new ExecuteRequestCommand('POST', 'https://sms.softeriatech.com/api/v1/sms.json', [
            'from' => 'YourCompany',
            'message' => 'Hello Kmer World! Déjà vu!',
            'to' => ['+237612345678'],
        ], new Credential($apikey . 'epi2', $apisecret . 'epi2'));
        $handler->expects($this->any())->method('handle')->with($command)
            ->will($this->throwException(new SmsproException('Test')));

        $oMessage->execRequest(HttpClient::POST_REQUEST, true, null);
    }

    /**
     * @covers \Smspro\Sms\Base::execRequest
     *
     * @dataProvider createDataProvider
     */
    public function testExecRequestXml($apikey, $apisecret)
    {
        $handler = $this->createMock(ExecuteRequestCommandHandler::class);
        $this->oBase->clear();
        /** @var Message|Base|Objects\Message $oMessage */
        $oMessage = Message::create($apikey, $apisecret, $handler);
        $oMessage->from = 'YourCompany';
        $oMessage->to = '+237612345678';
        $oMessage->message = 'Hello Kmer World! Déjà vu!';
        $oMessage->setResponseFormat('xml');

        $responseMock = $this->createMock(ResponseInterface::class);

        $command = new ExecuteRequestCommand('POST', 'https://sms.softeriatech.com/api/v1/sms.xml', [
            'from' => 'YourCompany',
            'message' => 'Hello Kmer World! Déjà vu!',
            'to' => ['+237612345678'],
        ], new Credential($apikey, $apisecret));
        $handler->expects($this->once())->method('handle')->with($command)

            ->will($this->returnValue(new \Smspro\Sms\Http\Response($responseMock, 'xml')));

        $oMessage->execRequest(HttpClient::POST_REQUEST);
    }

    /**
     * @covers \Smspro\Sms\Base::execRequest
     *
     * @dataProvider createDataProvider
     */
    public function testexecRequestEncFailure2($apikey, $apisecret)
    {
        file_put_contents('/tmp/test2.pem', 'TEST');
        $this->expectException(SmsproException::class);
        $this->assertNull($this->oBase->clear());
        $oMessage = Message::create($apikey, $apisecret);
        $oMessage->from = 'YourCompany';
        $oMessage->to = '+237612345678';
        $oMessage->encrypt = true;
        $oMessage->pgp_public_file = '/tmp/test2.pem';
        $oMessage->message = 'Hello Kmer World! Déjà vu!';
        $this->assertNotNull($oMessage->execRequest(HttpClient::POST_REQUEST, true, null));
    }

    /**
     * @covers \Smspro\Sms\Base::execBulk
     *
     * @dataProvider createDataProvider
     */
    public function testexecBulk($apikey, $apisecret)
    {
        $this->assertNull($this->oBase->clear());
        /** @var Objects\Message|Base $oMessage */
        $oMessage = Message::create($apikey, $apisecret);
        $oMessage->from = 'YourCompany';
        $oMessage->to = ['+237612345678', '+237612345679', '+237612345610', '+33689764530', '+4917612345671'];
        $this->assertIsInt($oMessage->execBulk([]));
    }

    public function testCanSetCredentials(): void
    {
        $base = Base::create();
        $this->assertInstanceOf(Base::class, $base->setCredential(new Credential('key', 'pass')));
        $this->assertEquals('key', $base->getCredentials()['pro_api_key']);
        $this->assertEquals('pass', $base->getCredentials()['api_secret']);
    }

    /**
     * @covers \Smspro\Sms\Base::execBulk
     *
     * @dataProvider createDataProvider
     */
    public function testexecBulkFailure($apikey, $apisecret)
    {
        $this->assertNull($this->oBase->clear());
        /** @var Objects\Message|Base $oMessage */
        $oMessage = Message::create($apikey, $apisecret);
        $oMessage->from = 'YourCompany';
        $oMessage->tel = ['+237612345678', '+237612345679', '+237612345610', '+33689764530', '+4917612345671'];
        $this->assertNull($oMessage->execBulk([]));
    }

    public function resourceDataProvider()
    {
        return [
            ['sms'],
            ['balance'],
        ];
    }

    public function formatDataProvider()
    {
        return [
            ['xml'],
            ['json'],
        ];
    }

    public function createDataProvider()
    {
        return [
            ['key1', 'secret1'],
            ['key2', 'secret2'],
        ];
    }
}
