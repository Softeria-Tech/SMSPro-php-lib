<?php

declare(strict_types=1);

use Smspro\Sms\Message;

require_once dirname(__DIR__) . '/vendor/autoload.php';
/**
 * Send BULK sms
 */

// OR (since version 3.3.1)
$oMessage = Message::create()->setCredential(new \Smspro\Sms\Entity\Credential('PRO_API_KEY'));

$oMessage->sender_name = 'smspro';
$oMessage->mobiles = ['254712509826','0753268299'];
$oMessage->message = 'Test message from SMSPRO';
var_dump($oMessage->sendBulk());

