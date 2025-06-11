<?php

declare(strict_types=1);

namespace Smspro\Sms\Entity;

enum FieldName: string
{
    case SENDER_ID = 'sender_name';
    case MESSAGE = 'message';
    case RECIPIENT = 'mobiles';

    case MESSAGE_ID = 'message_id';

    case RESPONSE = 'response';
}
