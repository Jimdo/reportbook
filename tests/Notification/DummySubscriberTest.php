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
            'dummyType'
        ];

        $userId = uniqid();
        $comment = new Comment(uniqid(), uniqid(), $userId, '11.11.11', 'Some content');
        $event = new CommentCreated($comment);

        $dummySubscriber = new DummySubscriber($eventTypes);
        $this->assertFalse($dummySubscriber->isResponsibleFor($event));

        $eventTypes = [
            'commentCreated'
        ];

        $dummySubscriber = new DummySubscriber($eventTypes);
        $this->assertTrue($dummySubscriber->isResponsibleFor($event));
    }
}
