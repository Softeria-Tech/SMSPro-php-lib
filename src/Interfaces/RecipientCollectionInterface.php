<?php

declare(strict_types=1);

namespace Smspro\Sms\Interfaces;

use IteratorAggregate;
use Stringable;

interface RecipientCollectionInterface extends IteratorAggregate, Stringable
{
    public function add(string $phoneNumber, ?string $name): void;

    public function toArray(): array;
}
