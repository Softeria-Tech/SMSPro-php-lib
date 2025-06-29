<?php

namespace Smspro\Test\TestCase\Objects;

use Smspro\Sms\Objects\Balance;
use PHPUnit\Framework\TestCase;
use Valitron\Validator;

/**
 * Class BalanceTest
 *
 * @author Softeria Tech
 *
 * @covers \Smspro\Sms\Objects\Balance
 */
class BalanceTest extends TestCase
{
    /**
     * @covers \Smspro\Sms\Objects\Balance::validatorDefault
     *
     * @dataProvider defaultDataProviderSuccess
     */
    public function testValidatorDefaultSuccess($payload)
    {
        $oValidator = (new Balance())->validatorDefault(new Validator($payload));
        $this->assertInstanceOf(Validator::class, $oValidator);
        $this->assertTrue($oValidator->validate());
    }

    /**
     * @covers \Smspro\Sms\Objects\Balance::validatorDefault
     *
     * @dataProvider defaultDataProviderFailure
     */
    public function testValidatorDefaultFailure($payload)
    {
        $oValidator = (new Balance())->validatorDefault(new Validator($payload));
        $this->assertInstanceOf(Validator::class, $oValidator);
        $this->assertFalse($oValidator->validate());
    }

    public function defaultDataProviderFailure(): array
    {
        return [
            [['amount' => 'bar', 'phonenumber' => '691243568']],
            [['amount' => 'foo', 'phonenumber' => '671243568']],
        ];
    }

    public function defaultDataProviderSuccess(): array
    {
        return [
            [['amount' => 3000, 'phonenumber' => '671243568']],
        ];
    }
}
