<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;

class DummyEventTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveType()
    {
        $eventName = 'dummyEvent';
        $event = new DummyEvent($eventName);

        $this->assertEquals($eventName, $event->type());
    }

    /**
     * @test
     */
    public function itShouldHavePayload()
    {
        $eventName = 'dummyEvent';
        $event = new DummyEvent($eventName);

        $expectedPayload = [
            'eventName' => $eventName,
        ];

        $this->assertEquals($expectedPayload, $event->payload());
    }
}
