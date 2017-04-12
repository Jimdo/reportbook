<?php

namespace Jimdo\Reports\Notification\Events;

use PHPUnit\Framework\TestCase;

class DummyEventTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveType()
    {
        $payload = [];
        $event = new DummyEvent($payload);

        $this->assertEquals('dummyEvent', $event->type());
    }

    /**
     * @test
     */
    public function itShouldHavePayload()
    {
        $payload = [];
        $event = new DummyEvent($payload);

        $this->assertEquals($payload, $event->payload());
    }
}
