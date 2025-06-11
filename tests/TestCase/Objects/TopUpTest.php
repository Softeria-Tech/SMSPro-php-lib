<?php

declare(strict_types=1);

namespace Smspro\Test\TestCase\Objects;

use Smspro\Sms\Objects\TopUp;
use PHPUnit\Framework\TestCase;
use Valitron\Validator;

/**
 * Class TopUpTest
 *
 * @author Softeria Tech
 *
 * @covers \Smspro\Sms\Objects\TopUp
 */
class TopUpTest extends TestCase
{
    /**
     * @covers \Smspro\Sms\Objects\TopUp::validatorDefault
     *
     * @dataProvider defaultDataProviderSuccess
     */
    public function testValidatorDefaultSuccess(array $payload): void
    {
        $oValidator = (new TopUp())->validatorDefault(new Validator($payload));
        $this->assertInstanceOf(Validator::class, $oValidator);
        $this->assertTrue($oValidator->validate());
    }

    /**
     * @covers \Smspro\Sms\Objects\TopUp::validatorDefault
     *
     * @dataProvider defaultDataProviderFailure
     */
    public function testValidatorDefaultFailure(array $payload): void
    {
        $oValidator = (new TopUp())->validatorDefault(new Validator($payload));
        $this->assertInstanceOf(Validator::class, $oValidator);
        $this->assertFalse($oValidator->validate());
    }

    public function defaultDataProviderFailure(): array
    {
        return [
            [['amount' => 1000, 'to' => '691243568']],
            [['amount' => 'foo', 'phonenumber' => '671243568']],
            [['amount' => '1500', 'to' => '671243568']],
            [['amount' => 2999, 'phonenumber' => '671243568']],
            [['amount' => 2999.99, 'phonenumber' => '691243568']],
        ];
    }

    public function defaultDataProviderSuccess(): array
    {
        return [
            [['amount' => 3000, 'phonenumber' => '671243568']],
            [['amount' => 4000, 'phonenumber' => '691243568']],
        ];
    }
}
