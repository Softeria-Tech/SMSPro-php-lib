<?php

declare(strict_types=1);

namespace Smspro\Sms\Objects;

/**
 * Softeria Tech Ltd: http://sms.softeriatech.com
 *
 * @copyright (c) sms.softeriatech.com
 * @license You are not allowed to sell or distribute this software without permission
 * Copyright reserved
 * File: src/Objects/Balance.php
 * updated: Jan 2018
 * Description: SMSPRO SMS message Objects
 *
 * @link http://sms.softeriatech.com
 */

use Valitron\Validator;

final class Balance extends Base
{
    /** Only available for MTN Mobile Money Cameroon */
    public string $phonenumber;

    /** amount that should be recharged */
    public int|float|null $amount = null;

    public function validatorDefault(Validator $validator): Validator
    {
        $validator
            ->rule('required', ['phonenumber', 'amount']);
        $validator
            ->rule('integer', 'amount');

        return $validator;
    }
}
