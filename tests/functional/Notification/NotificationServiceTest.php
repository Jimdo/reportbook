<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Reportbook\Comment as Comment;

class NotificationServiceTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldNotifySubscriber()
    {
        $eventTypes = [
            'dummyEvent'
        ];

        $notificationService = new NotificationService();
        $subscriber = new DummySubscriber($eventTypes);
        $event = new Events\DummyEvent([]);

        $notificationService->register($subscriber);
        $notificationService->notify($event);

        $this->assertTrue($subscriber->notified);
    }
}
