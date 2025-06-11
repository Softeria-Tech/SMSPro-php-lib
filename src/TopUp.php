<?php

declare(strict_types=1);

namespace Smspro\Sms;

use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Http\Client;
use Throwable;

class TopUp extends Base
{
    /**
     * Initiates a top-up to recharge a user account
     *
     * Only available for MTN and Orange Mobile Money Cameroon
     *
     * @throws Exception\SmsproException
     */
    public function add(): Response\TopUp
    {
        try {
            $this->setResourceName(Constants::RESOURCE_TOP_UP);

            return new Response\TopUp($this->execRequest(Client::POST_REQUEST));
        } catch (Throwable $exception) {
            throw new SmsproException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
