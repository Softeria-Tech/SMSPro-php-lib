<?php

declare (strict_types = 1);

namespace Smspro\Sms\Objects;

/**
 * Softeria Tech Ltd: http://sms.softeriatech.com
 *
 * @copyright (c) sms.softeriatech.com
 * @license You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Objects/Message.php
 * updated: Dec 2017
 * Description: SMSPRO SMS message Objects
 *
 * @link http://sms.softeriatech.com
 */

use Smspro\Sms\Entity\Recipient;
use Valitron\Validator;

final class Message extends Base
{
    /**
     * The sender of the message. This can be a telephone number
     * (including country code) or an alphanumeric string. In case
     * of an alphanumeric string, the maximum length is 11 characters.
     */
    public ?string $sender_name = null;

    /** The content of the SMS message. */
    public ?string $message = null;

    /**
     * Recipient that should receive the sms
     * You can set single recipient (string) or multiple recipients by using array
     */
    public iterable|Recipient|string|null $mobiles = null;

    /** The datacoding used, can be text,plain,unicode or auto */
    public ?string $datacoding = null;

    /**
     * The SMS route that is used to send the message, can be premium, classic. Default: premium
     * This optional parameter works only for cameroonian mobile phone numbers.
     */
    public ?string $route = null;

    /** The type of message. Values can be: sms, binary or flash . Default: sms */
    public ?string $type = null;

    /** A client reference. It might be whatever you want to identify the message. */
    public ?string $reference = null;

    /**
     * The amount of seconds, that the message is valid.
     * If a message is not delivered within this time, the message will be discarded. Should be greater than 30
     */
    public ?int $validity = null;

    /**
     * Encrypt message before sending.
     * Highly recommended if you are sending SMS for two-factor authentication. Default : false
     */
    public bool $encrypt = false;

    /** Public PGP file to Encrypt message before sending (Optional). */
    public ?string $pgp_public_file = null;

    /** Handle a status rapport. For more information: https://github.com/smspro/sms/wiki/Handle-a-status-rapport */
    public ?string $notify_url = null;

    /**
     * A unique random ID which is created on Smspro SMS
     * platform and is returned for the created object.
     */
    public ?string $id = null;

    public function validatorDefault(Validator $validator): Validator
    {
        $validator->rule('required', ['sender_name', 'message', 'mobiles']);
        $validator
            ->rule('optional', [
                'type',
                'datacoding',
                'route',
                'encrypt',
                'reference',
                'validity',
                'notify_url',
                'pgp_public_file',
            ]);
        $validator
            ->rule('in', 'type', ['sms', 'binary', 'flash']);
        $validator
            ->rule('in', 'datacoding', ['plain', 'text', 'unicode', 'auto']);
        $validator
            ->rule('in', 'route', ['premium', 'classic']);
        $validator
            ->rule('boolean', 'encrypt');
        $validator
            ->rule('lengthMax', 'reference', 32);
        $validator
            ->rule('integer', 'validity');
        $validator
            ->rule('min', 'validity', 30);
        $validator
            ->rule('lengthMax', 'notify_url', 200);
        $validator
            ->rule('url', 'notify_url');
        $validator
            ->rule(function (string $field, string $value) {
                return is_file($value);
            }, 'pgp_public_file')->message('{field} does not exist');
        $this->isPossibleNumber($validator, 'mobiles');
        $this->isValidUTF8Encoded($validator, 'sender_name');
        $this->isValidUTF8Encoded($validator, 'message');

        return $validator;
    }

    public function validatorView(Validator $oValidator): Validator
    {
        $oValidator
            ->rule('required', ['id']);
        $this->notEmptyRule($oValidator, 'id');

        return $oValidator;
    }
}
