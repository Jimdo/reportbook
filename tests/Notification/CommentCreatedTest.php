<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Reportbook\Comment as Comment;

class CommentCreatedTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveType()
    {
        $type = 'commentCreated';
        $userId = uniqid();
        $comment = new Comment(uniqid(), uniqid(), $userId, '11.11.11', 'Some content');

        $event = new CommentCreated($comment);

        $this->assertEquals($type, $event->type());
    }

    /**
     * @test
     */
    public function itShouldHavePayload()
    {
        $type = 'commentCreated';
        $userId = uniqid();
        $comment = new Comment(uniqid(), uniqid(), $userId, '11.11.11', 'Some content');

        $event = new CommentCreated($comment);

        $expectedPayload = [
            'userId' => $comment->userId(),
            'content' => $comment->content()
        ];

        $this->assertEquals($expectedPayload, $event->payload());
    }
}
