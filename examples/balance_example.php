<?php

declare(strict_types=1);

use Smspro\Sms\Balance;

require_once dirname(__DIR__) . '/vendor/autoload.php';
/**
 * @Brief read current balance
 */
/** @var Balance $oBalance */
$oBalance = Balance::create('YOUR_API_KEY');

$response = $oBalance->get();

$balance = $response->getBalance();
$currency = $response->getCurrency();

$rate = $response->getRate();

echo "Your current balance is {$balance} {$currency} at a rate of {$rate}.\n";