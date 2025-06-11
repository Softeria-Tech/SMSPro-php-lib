<?php

declare(strict_types=1);

namespace Smspro\Sms\Entity;

use Smspro\Sms\Objects\Balance;
use Smspro\Sms\Objects\Message;
use Smspro\Sms\Objects\ObjectEntityInterface;
use Smspro\Sms\Objects\TopUp;

enum ObjectEntity
{
    public function getInstance(): ObjectEntityInterface
    {
        return match ($this) {
            self::MESSAGE => new Message(),
            self::BALANCE => new Balance(),
            self::TOPUP => new TopUp()
        };
    }

    case BALANCE;
    case MESSAGE;
    case TOPUP;
}
