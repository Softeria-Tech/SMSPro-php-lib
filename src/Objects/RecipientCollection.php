<?php

declare(strict_types=1);

namespace Smspro\Sms\Objects;

use ArrayObject;
use Smspro\Sms\Entity\Recipient;
use Smspro\Sms\Interfaces\RecipientCollectionInterface;
use Smspro\Sms\Lib\Utils;
use Traversable;

final class RecipientCollection implements RecipientCollectionInterface
{
    /** @var array<Recipient> $recipients */
    private array $recipients = [];

    /** @var array<string|int> $recipients */
    public function __construct(array $recipients = [])
    {
        $this->initializeRecipients($recipients);
    }

    public function __toString(): string
    {
        return implode(',', array_keys($this->recipients));
    }

    public function add(string $phoneNumber, ?string $name = null): void
    {
        $index = preg_replace('/[^\dxX]/', '', $phoneNumber);
        $this->recipients[$index] = new Recipient($phoneNumber, $name);
    }

    /** @codeCoverageIgnore */
    public function getIterator(): Traversable
    {
        return new ArrayObject($this->recipients);
    }

    public function toArray(): array
    {
        return array_map(fn (Recipient $recipient) => [
            'mobile' => $recipient->phoneNumber,
            'name' => $recipient->name,
        ], $this->recipients);
    }

    private function initializeRecipients(array $recipients): void
    {
        foreach ($recipients as $recipient) {
            $name = Utils::getRecipientName($recipient);
            $phoneNumber = Utils::phoneNumberE164Format($recipient);
            $this->add($phoneNumber, $name);
        }
    }
}
