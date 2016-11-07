<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Notification\CommentCreated as CommentCreated;
use Jimdo\Reports\Reportbook\Comment as Comment;

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

    /**
     * @test
     */
    public function itShouldHaveIsResponsibleFor()
    {
        $eventTypes = [
            'dummyEvent'
        ];

        $eventName = 'dummyEvent';
        $event = new Events\DummyEvent($eventName);

        $dummySubscriber = new DummySubscriber($eventTypes);
        $this->assertTrue($dummySubscriber->isResponsibleFor($event));
    }

    /**
     * @test
     */
    public function itShouldNotifyOnRightEventType()
    {
        $eventTypes = [
            'dummyEvent'
        ];

        $eventName = 'dummyEvent';
        $event = new Events\DummyEvent($eventName);

        $dummySubscriber = new DummySubscriber($eventTypes);

        $dummySubscriber->notify($event);

        $this->assertTrue($dummySubscriber->notified);
    }
}
