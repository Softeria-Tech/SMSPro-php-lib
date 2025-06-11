<?php

declare(strict_types=1);

namespace Smspro\Sms;

/**
 * Softeria Tech Ltd : http://sms.softeriatech.com
 *
 * @copyright (c) sms.softeriatech.com
 * @license You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Balance.php
 * Updated: Jan. 2018
 * Created by: Softeria Tech Ltd (support@softeriatech.com)
 * Description: SMSPRO SMS LIB
 *
 * @link http://sms.softeriatech.com
 */

/**
 * Class Smspro\Sms\Balance
 * Get or add balance to your account
 */

use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Http\Client;
use Throwable;

class Balance extends Base
{
    /**
     * read the current user balance
     *
     * @return Response\Balance Balance
     */
    public function get(): Response\Balance
    {
        try {
            $this->setResourceName(Constants::RESOURCE_BALANCE);

            return new Response\Balance($this->execRequest(Client::GET_REQUEST, false));
        } catch (Throwable) {
            throw new SmsproException('Balance Request can not be performed!');
        }
    }
}
