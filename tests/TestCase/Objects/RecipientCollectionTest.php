<?php

declare(strict_types=1);

namespace Smspro\Test\TestCase\Objects;

use Smspro\Sms\Entity\Recipient;
use Smspro\Sms\Objects\RecipientCollection;
use PHPUnit\Framework\TestCase;

final class RecipientCollectionTest extends TestCase
{
    private ?RecipientCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = new RecipientCollection(['0112587956', '+254712509826', '753268299', '254712345678']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->collection = null;
    }


    public function testCanAdd(): void
    {
        $collection = new RecipientCollection();
        $collection->add('254712345678');
        
        $array = iterator_to_array($collection);
        $this->assertEquals($array[254712345678], new Recipient('254712345678'));
    }

    public function testWithoutRecipients(): void
    {
        $collection = new RecipientCollection();
        $this->assertCount(0, $collection);
    }
}
