<?php

declare(strict_types=1);

namespace Smspro\Sms;

/**
 * Softeria Tech Ltd: http://sms.softeriatech.com
 *
 * @copyright (c) sms.softeriatech.com
 * @license You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Message.php
 * Updated: Jan. 2018
 * Created by: Softeria Tech Ltd (support@softeriatech.com)
 * Description: SMSPRO SMS LIB
 *
 * @link http://sms.softeriatech.com
 */

/**
 * Class Smspro\Sms\Message handles the methods and properties of sending an SMS message.
 */

use Smspro\Sms\Entity\CallbackDto;
use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Exception\RateLimitException;
use Smspro\Sms\Http\Client;
use Smspro\Sms\Response\Message as MessageResponse;
use Exception;
use Throwable;

class Message extends Base
{
    /**
     * Send Message
     *
     * @throws SmsproException
     *
     * @return Response\Message Message Response
     */
    public function send(): MessageResponse
    {
        try {
            $this->setResourceName(Constants::RESOURCE_SEND_SMS);
            $response = $this->execRequest(Client::POST_REQUEST);
        } catch (RateLimitException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new SmsproException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        return new MessageResponse($response);
    }

    /**
     * view a message sent
     *
     * @throws SmsproException
     *
     * @return Response\Message Message
     */
    public function view(): MessageResponse
    {
        try {
            $this->setResourceName(Constants::RESOURCE_VIEW);

            return new Response\Message(
                $this->execRequest(Client::GET_REQUEST, true, Constants::RESOURCE_VIEW)
            );
        } catch (Throwable $exception) {
            throw new SmsproException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * sends bulk message
     *
     * @throws Exception
     */
    public function sendBulk()
    {
        return $this->send();
    }
}
