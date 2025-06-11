<?php

declare(strict_types=1);

namespace Smspro\Sms\Console;

use Smspro\Sms\Constants;
use Smspro\Sms\Entity\PersonalizedContent;
use Smspro\Sms\Lib\Utils;

final class PersonalizationCommandHandler
{
    /** Handle the personalization command and return the personalized content. */
    public function handle(PersonalizationCommand $command): PersonalizedContent
    {
        $destination = $command->destination;
        $message = $command->message;

        // Extract name from the destination array, if available.
        $name = $this->extractNameFromDestination($destination);

        // Personalize the message with the name.
        $message = $this->personalizeMessage($message, $name);

        // Extract mobile from the destination array, if available.
        if (is_array($destination) && isset($destination['mobile'])) {
            $destination = $destination['mobile'];
        }

        return new PersonalizedContent($destination, $message);
    }

    /** Extract name from the destination array, sanitize it, and return. */
    private function extractNameFromDestination(mixed $destination): string
    {
        if (is_array($destination) && isset($destination['name'])) {
            return Utils::satanizer($destination['name']);
        }

        return '';
    }

    /** Replace placeholders in the message with provided name and return the personalized message. */
    private function personalizeMessage(?string $message, string $name): ?string
    {
        return $message ? str_replace(Constants::PERSONALIZE_MSG_KEYS, [$name], $message) : null;
    }
}
