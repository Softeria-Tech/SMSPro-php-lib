<?php

namespace Smspro\Test\TestCase\Objects;

use Smspro\Sms\Entity\ObjectEntity;
use Smspro\Sms\Entity\Recipient;
use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Lib\Utils;
use Smspro\Sms\Objects\Balance;
use Smspro\Sms\Objects\Base;
use Smspro\Sms\Objects\Message;
use Smspro\Sms\Objects\RecipientCollection;
use PHPUnit\Framework\TestCase;
use Valitron\Validator;

/**
 * Class BaseTest
 *
 * @author Softeria Tech
 *
 * @covers Base
 */
class BaseTest extends TestCase
{
    private ?Base $oBase;

    public function setUp(): void
    {
        $this->oBase = Base::create();
    }

    public function tearDown(): void
    {
        unset($this->oBase);
    }

    /**
     * @covers Base::create
     *
     * @runInSeparateProcess
     */
    public function testCreate()
    {
        $this->assertInstanceOf(Base::class, Base::create());
    }

    /**
     * @covers Base::set
     *
     * @runInSeparateProcess
     *
     * @dataProvider setDataProviderSuccess
     */
    public function testSetSuccess($property, $value, $object)
    {
        $this->oBase->set($property, $value, $object);
        if ($property === 'to') {
            $value = Utils::makeNumberE164Format($value);
        }
        $this->assertEquals($value, $object->{$property});
    }

    /**
     * @covers Base::set
     *
     * @runInSeparateProcess
     *
     * @testWith        ["test", 4, null]
     */
    public function testSetNull($property, $value, $object)
    {
        $this->assertNull($this->oBase->set($property, $value, $object));
    }

    /**
     * @covers Base::set
     *
     * @runInSeparateProcess
     *
     * @dataProvider setDataProviderFailure
     */
    public function testSetFailure($property, $value, $object)
    {
        $this->expectException(SmsproException::class);
        $this->oBase->set($property, $value, $object);
    }

    /**
     * @covers Base::get
     *
     * @runInSeparateProcess
     *
     * @dataProvider getDataProviderSuccess
     */
    public function testGetSuccess(array $sets, ObjectEntity $object): void
    {
        $base = $this->oBase;
        $entity = $object->getInstance();
        foreach ($sets as $key => $value) {
            $base->set($key, $value, $entity);
        }

        $result = $base->get($entity);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * @covers Base::get
     *
     * @runInSeparateProcess
     *
     * @dataProvider getDataProviderFailure
     */
    public function testGetFailure(array $sets, ObjectEntity $object): void
    {
        $this->expectException(SmsproException::class);
        $entity = $object->getInstance();
        array_map(function (string $key, mixed $set) use ($entity) {
            $this->oBase->set($key, $set, $entity);
        }, array_keys($sets), array_values($sets));

        $this->oBase->get($entity);
    }

    /**
     * @runInSeparateProcess
     *
     * @dataProvider isUTF8DataProviderFailure
     */
    public function testIsValidUTF8Encoded($message): void
    {
        $oValidator = new Validator(['message' => $message]);
        Base::create()->isValidUTF8Encoded($oValidator, 'message');
        $this->assertFalse($oValidator->validate());
    }

    /**
     * @covers Base::has
     *
     * @runInSeparateProcess
     *
     * @testWith        ["to"]
     *                  ["tel"]
     */
    public function testHas($property): void
    {
        $this->assertIsBool(Message::create()->has($property));
    }

    /**
     * @covers Base::isPossibleNumber
     *
     * @runInSeparateProcess
     *
     * @testWith        ["0"]
     *                  [["9999"]]
     *                  [["8888"]]
     */
    public function testIsPossibleNumberFailure($to): void
    {
        $oValidator = new Validator(['message' => 'foo', 'from' => 'Bar', 'to' => $to]);
        $this->assertNull(Base::create()->isPossibleNumber($oValidator, 'to'));
        $this->assertFalse($oValidator->validate());
    }

    /**
     * @covers Base::notEmptyRule
     *
     * @runInSeparateProcess
     *
     * @testWith        ["0"]
     *                  ["tel"]
     *                  [""]
     */
    public function testNotEmptyRule($value): void
    {
        $oValidator = new Validator(['id' => $value]);
        Base::create()->notEmptyRule($oValidator, 'id');
        $this->assertIsBool($oValidator->validate());
    }

    /** @dataProvider provideValidePossibleNumbers */
    public function testIsPossiblePhoneNumber(mixed $to): void
    {
        $data = [
            'to' => [$to],
        ];
        $validator = new Validator($data);
        $this->oBase->isPossibleNumber($validator, 'to');
        $this->assertTrue($validator->validate());
    }

    public function testIsPossibleNumberValid(): void
    {
        $base = new Base();
        $validator = new Validator(['phone_numbers' => ['+237655556666', '+237655557777']]);

        $base->isPossibleNumber($validator, 'phone_numbers');

        $this->assertTrue($validator->validate());
    }

    public function testGetWithValidMessageEntityAndMultiArray(): void
    {
        $base = new Base();
        $message = new Message();
        $message->to = [['mobile' => '+237655556666'], ['mobile' => '+237691234567']];
        $message->route = 'classic';
        $message->from = 'classic';
        $message->message = 'classic';

        $result = $base->get($message);
        $this->assertArrayHasKey('to', $result);
    }

    public function testGetWithValidMessageEntityAndRecipientCollection(): void
    {
        $base = new Base();
        $message = new Message();
        $recipient = new RecipientCollection();
        $recipient->add('+237655556666');
        $recipient->add('+237691234567');
        $message->to = $recipient;
        $message->route = 'classic';
        $message->from = 'classic';
        $message->message = 'classic';

        $result = $base->get($message);
        $this->assertArrayHasKey('to', $result);
    }

    public function testGetWithValidMessageEntityAndMixRecipientCollection(): void
    {
        $base = new Base();
        $message = new Message();
        $recipient = new RecipientCollection();
        $recipient->add('+237655556666');
        $recipient->add('+237691234567');
        $message->to = [$recipient, '+237691234562'];
        $message->route = 'classic';
        $message->from = 'classic';
        $message->message = 'classic';

        $result = $base->get($message);
        $this->assertArrayHasKey('to', $result);
    }

    public function testGetWithInvalidCameroonianPhoneNumber(): void
    {
        $this->expectException(SmsproException::class);
        $this->expectExceptionMessage('{"to":["To no (correct) phone number found!"]}');

        $base = new Base();
        $message = new Message();  // Assuming this exists.
        $message->to = ['+1234555666'];
        $message->route = 'classic';
        $message->from = 'classic';
        $message->message = 'classic';

        $base->get($message);
    }

    public function testGetWithMixRecipientsAndInvalidToThrowsException(): void
    {
        $this->expectException(SmsproException::class);
        $this->expectExceptionMessage('{"to":["To no (correct) phone number found!"]}');

        $base = new Base();
        $message = new Message();
        $recipient = new RecipientCollection();
        $recipient->add('+1(invalid)234567');
        $recipient->add('+237691234567');
        $message->to = [$recipient, '+237691234562'];
        $message->from = 'classic';
        $message->message = 'classic';

        $base->get($message);
    }

    /**
     * @covers Base::canTopUpCM
     *
     * @runInSeparateProcess
     *
     * @dataProvider provideInvalidTopUpData
     */
    public function testCanTopUpCMFailure(string $to, string $field): void
    {
        $oValidator = new Validator([$field => $to]);
        Base::create()->canTopUpCM($oValidator, $field);
        $this->assertFalse($oValidator->validate());
    }

    /**
     * @covers Base::canTopUpCM
     *
     * @runInSeparateProcess
     *
     * @dataProvider provideValidTopUpData
     */
    public function testCanTopUpCM(string $to): void
    {
        $oValidator = new Validator(['phonenumber' => $to]);
        Base::create()->canTopUpCM($oValidator, 'phonenumber');
        $this->assertTrue($oValidator->validate());
    }

    public function provideValidTopUpData(): array
    {
        return [
            ['673123456'],
            ['693123456'],
        ];
    }

    public function provideInvalidTopUpData(): array
    {
        return [
            ['0', 'phonenumber'],
            ['193123456', 'phonenumber'],
            ['693123456', 'to'],
        ];
    }

    public function getDataProviderSuccess(): array
    {
        return [
            [['to' => '237612345678', 'from' => 'Foo', 'message' => 'Foo Bar'],  ObjectEntity::MESSAGE],

            [['to' => [
                '237612345678',
                '237612345671',
                '237612345672',
                '237612345673',

            ], 'from' => 'Foo', 'message' => 'Foo Bar'],  ObjectEntity::MESSAGE],

            [['to' => '237672345678', 'from' => 'Foo', 'message' => 'Foo Bar', 'route' => 'classic'],  ObjectEntity::MESSAGE],

            [['phonenumber' => '672345678', 'amount' => 3000], ObjectEntity::BALANCE],
        ];
    }

    public function getDataProviderFailure(): array
    {
        return [
            [['to' => '612345678', 'from' => 'Foo', 'message' => 'Foo Bar'], ObjectEntity::MESSAGE],
            [
                ['to' => [
                    '33672345678',
                ], 'from' => 'Foo', 'message' => 'Foo Bar', 'route' => 'classic'], ObjectEntity::MESSAGE,
            ],
            [['phonenumber' => '237692345678', 'amount' => 3000.01], ObjectEntity::BALANCE],
        ];
    }

    public function setDataProviderSuccess(): array
    {
        return [
            ['from', 'FooBar', new Message()],
            ['to', '237612345678', new Message()],
            ['to', ['237612345678', '33612345678'], new Message()],
            ['to', [['mobile' => '237612345678', 'name' => 'John Doe'], ['mobile' => '33612345678', 'Jeanne Doe']], new Message()],
            ['phonenumber', '612345678', new Balance()],
        ];
    }

    public function setDataProviderFailure(): array
    {
        return [
            ['sender', 'FooBar', new Message()],
            ['phone', '237612345678', new Message()],
            ['tel', ['237612345678', '33612345678'], new Message()],
            ['sms', [['mobile' => '237612345678', 'name' => 'John Doe'], ['mobile' => '33612345678', 'Jeanne Doe']], new Message()],
            ['to', '612345678', new Balance()],
        ];
    }

    public function isUTF8DataProviderFailure(): array
    {
        return [
            [file_get_contents(dirname(__DIR__, 2) . '/Fixture/UTF-8-test.txt')],
        ];
    }

    public function provideValidePossibleNumbers(): array
    {
        return [
            iterator_to_array(new RecipientCollection(['237693123456'])),
            [new Recipient('237693123456')],
            ['mobile' => '237693123456'],
            ['237693123456'],
        ];
    }
}
