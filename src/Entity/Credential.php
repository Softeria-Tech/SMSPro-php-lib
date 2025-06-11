<?php

declare(strict_types=1);

namespace Smspro\Sms\Entity;

final class Credential
{
    public function __construct(public readonly string $key, public $secret=null)
    {
    }

    public function toArray(): array
    {
        return [
            'pro_api_key' => $this->key ?: '',
            'api_secret' => $this->secret ?: '',
        ];
    }
}
