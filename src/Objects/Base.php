<?php

declare(strict_types=1);

namespace Smspro\Sms\Objects;

use Smspro\Sms\Entity\Recipient;
use Smspro\Sms\Exception\SmsproException;
use Smspro\Sms\Lib\Utils;
use Valitron\Validator;

/**
 * Class Objects\Base
 */
class Base implements ObjectEntityInterface
{
    private const CM_CARRIERS = ['MTN', 'ORANGE', 'CAMTEL'];

    public static function create(): self
    {
        return new static();
    }

    public function set(string $property, mixed $value, ?ObjectEntityInterface $class = null): void
    {
        if ($class === null) {
            return;
        }

        if (!property_exists($class, $property)) {
            throw new SmsproException([$property => 'is not allowed!']);
        }
        if ($property === 'sender_name') {
            $value = Utils::clearSender($value);
        }
        if ($property === 'mobiles') {
            $value = Utils::makeNumberE164Format($value);
        }
        $class->$property = $value;
    }

    public function get(ObjectEntityInterface $entity, string $validator = 'default'): array
    {
        $payload = get_object_vars($entity);
        $validationMethod = 'validator' . ucfirst($validator);
        if (method_exists($entity, $validationMethod)) {
            /** @var Validator $validation */
            $validation = $entity->$validationMethod(new Validator($payload));
            if (!$validation->validate()) {
                throw new SmsproException($validation->errors());
            }
        }
        $this->validateCameroonPhoneNumber($payload, $entity);

        return array_filter($payload);
    }

    public function isValidUTF8Encoded(Validator $oValidator, string $sParam): void
    {
        $oValidator
            ->rule(function (string $field, mixed $value) {
                return mb_check_encoding($value, 'UTF-8');
            }, $sParam)->message('{field} needs to be a valid UTF-8 encoded string');
    }

    public function notEmptyRule(Validator $oValidator, string $parameter): void
    {
        $oValidator
            ->rule(function (string $field, mixed $value) {
                return !empty($value);
            }, $parameter)->message('{field} can not be blank/empty...');
    }

    public function isPossibleNumber(Validator $oValidator, string $parameter): void
    {
        $oValidator
            ->rule(function (string $field, mixed $value): bool {
                if (empty($value) || empty($field)) {
                    return false;
                }

                return $this->validatePhoneNumberOrNumbers($value);
            }, $parameter)->message('{field} no (correct) phone number found!');
    }

    public function has(string $property): bool
    {
        return property_exists($this, $property);
    }

    public function canTopUpCM(Validator $validator, string $parameter): void
    {
        $validator
            ->rule(function (string $field, mixed $value) {
                if (empty($value)) {
                    return false;
                }
                if ($field !== 'phonenumber') {
                    return false;
                }

                return in_array(Utils::getPhoneCarrier($value), self::CM_CARRIERS, true);
            }, $parameter)->message('{field} is not carried by MTN or Orange Cameroon');
    }

    /** @codeCoverageIgnore */
    public function validatorDefault(Validator $validator): Validator
    {
        return $validator;
    }
    // @codeCoverageIgnoreEnd

    /** Validates if the given value(s) are possible phone numbers. */
    private function validatePhoneNumberOrNumbers(mixed $value): bool
    {
        $phoneNumbers = is_array($value) ? $value : [$value];
        foreach ($phoneNumbers as $xTo) {
            $sTo = $this->findPhoneNumber($xTo);
            if (str_contains($sTo, ',')) {
                if (!$this->validateMultiplePhoneNumbers($sTo)) {
                    return false;
                }
            } else {
                if (!$this->isValidPhoneNumber($sTo)) {
                    return false;
                }
            }
        }

        return true;
    }

    /** Validates multiple phone numbers separated by commas. */
    private function validateMultiplePhoneNumbers(string $numbers): bool
    {
        $explodedNumbers = array_map(fn (string $num) => trim($num), explode(',', $numbers));

        return $this->validatePhoneNumbers($explodedNumbers);
    }

    private function validatePhoneNumbers(array $numbers): bool
    {
        foreach ($numbers as $number) {
            if (!$this->isValidPhoneNumber($number)) {
                return false;
            }
        }

        return true;
    }

    private function isValidPhoneNumber(string $phoneNumber): bool
    {
        $xTel = preg_replace('/[^\dxX]/', '', $phoneNumber);
        $xTel = ltrim($xTel, '0');

        return is_numeric($xTel) && (mb_strlen($xTel) > 8 && mb_strlen($xTel) <= 15);
    }

    private function findPhoneNumber(mixed $recipient): string
    {
        if (is_string($recipient)) {
            return trim($recipient);
        }

        if ($recipient instanceof Recipient) {

            return $recipient->phoneNumber;
        }

        return is_array($recipient) && isset($recipient['mobile']) ? $recipient['mobile'] : (string)$recipient;
    }

    private function validateCameroonPhoneNumber(array $payload, ObjectEntityInterface $entity): void
    {
        if (!$entity instanceof Message || !array_key_exists('route', $payload) || $payload['route'] !== 'classic') {
            return;
        }

        foreach ($payload['mobiles'] as $xTo) {
            if ($xTo instanceof RecipientCollection) {
                $this->validateCameroonPhoneNumber($xTo->toArray(), $entity);
                continue;
            }

            if (!Utils::isValidPhoneNumber($xTo, 'CM', true)) {
                throw new SmsproException([
                    json_encode($xTo) => 'does not seem to be a Cameroonian phone number!',
                ]);
            }
        }
    }
}
