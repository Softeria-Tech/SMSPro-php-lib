<?php

declare(strict_types=1);

namespace Smspro\Sms\Entity;

use Smspro\Sms\Balance;
use Smspro\Sms\Http\Command\ExecuteRequestCommandHandler;
use Smspro\Sms\Message;
use Smspro\Sms\ObjectHandlerInterface;
use Smspro\Sms\TopUp;

enum ObjectHandler
{
    public function getInstance(?ExecuteRequestCommandHandler $handler): ObjectHandlerInterface
    {
        return match ($this) {
            self::MESSAGE => new Message($handler),
            self::BALANCE => new Balance($handler),
            self::TOPUP => new TopUp($handler)
        };
    }

    case BALANCE;
    case MESSAGE;
    case TOPUP;
}
