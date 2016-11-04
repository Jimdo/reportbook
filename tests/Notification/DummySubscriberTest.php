<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Notification\Event as Event;

class DummySubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveValidEventTypes()
    {
        $eventTypes = [
            'dummyType'
        ];

        $dummySubscriber = new DummySubscriber($eventTypes);

        $this->assertEquals($eventTypes, $dummySubscriber->validEventTypes);
    }
}
