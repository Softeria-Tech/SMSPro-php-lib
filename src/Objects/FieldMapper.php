<?php

declare(strict_types=1);

namespace Smspro\Sms\Objects;

use Smspro\Sms\Entity\FieldName;
use Smspro\Sms\Entity\TableMapping;
use Smspro\Sms\Response\Message;

final class FieldMapper
{
    public function __construct(
        private readonly TableMapping $tableMapping,
        private readonly Message $messageResponse,
        private readonly ?string $senderId = null
    ) {
    }

    public function get(): array
    {
        $fields = [];
        foreach ($this->tableMapping->toArray() as $driverKey => $appKey) {
            if (empty($appKey)) {
                continue;
            }

            $fields[$appKey] = match ($driverKey) {
                FieldName::MESSAGE->value => $this->messageResponse->getMessage(),
                FieldName::RECIPIENT->value => $this->messageResponse->getTo(),
                FieldName::MESSAGE_ID->value => $this->messageResponse->getId(),
                FieldName::SENDER_ID->value => $this->senderId ?? '',
                FieldName::RESPONSE->value => json_encode($this->messageResponse->jsonSerialize()),
                default => null,
            };
        }

        return $fields;
    }
}
