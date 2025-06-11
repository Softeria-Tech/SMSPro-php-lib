<?php

declare(strict_types=1);

namespace Smspro\Test\TestCase\Console;

use Smspro\Sms\Console\OperatingSystem;
use PHPUnit\Framework\TestCase;

class OperatingSystemTest extends TestCase
{
    public function testCanGet(): void
    {
        $system = new OperatingSystem();
        $this->assertNotEmpty($system->get());
    }
}
