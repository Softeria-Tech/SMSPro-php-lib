<?php

declare(strict_types=1);

namespace Smspro\Sms\Response;

use Smspro\Sms\Entity\Money;
use Smspro\Sms\Http\Response;

/**
 * Class Balance
 *
 * @property float  $balance
 * @property string $currency
 *
 * @author Softeria Tech
 */
final class Balance extends ObjectResponse
{
    public function __construct(Response $response)
    {
        parent::__construct($response);
    }

    public function getBalance(): float
    {
        if (array_key_exists('credit_balance', $this->_data)) {
            return (float)str_replace(",","",$this->_data['credit_balance']);
        }

        return 0.00;
    }

    public function getRate(): float
    {
        if (array_key_exists('rate', $this->_data)) {
            return (float)$this->_data['rate'];
        }

        return 1.0;
    }

    public function getCurrency(): ?string
    {
        return $this->_data['currency']??"Ksh";;
    }

    public function getMoney(): Money
    {
        return new Money($this->getBalance(), $this->getCurrency());
    }
}
