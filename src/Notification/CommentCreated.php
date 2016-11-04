<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Reportbook\Comment as Comment;

class CommentCreated implements Event
{
    /** @var string */
    private $userId;

    /** @var string */
    private $content;

    /**
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->userId = $comment->userId();
        $this->content = $comment->content();
    }
    /**
     * @return string
     */
    public function type(): string
    {
        return 'commentCreated';
    }

    /**
     * @return array
     */
    public function payload(): array
    {

    }
}
