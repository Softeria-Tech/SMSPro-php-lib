<?php

declare(strict_types=1);

namespace Smspro\Sms\Entity;

enum FieldName: string
{
    case SENDER_ID = 'from';
    case MESSAGE = 'message';
    case MESSAGE_ID = 'message_id';
    case RECIPIENT = 'to';

    case RESPONSE = 'response';
}
