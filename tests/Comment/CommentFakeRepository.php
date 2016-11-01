<?php

namespace Jimdo\Reports\Comment;

class CommentFakeRepository implements CommentRepository
{
    /** @var Comments[] */
    public $comments = [];

    /** @var Comment */
    public $newComment;

    /** @var bool */
    public $saveMethodCalled = false;

    /**
     * @param string $reportId
     * @param string $userId
     * @param string $date
     * @param string $content
     * @return Comment
     */
    public function createComment(string $reportId, string $userId, string $date, string $content): Comment
    {
        $comment = new Comment($reportId, $userId, $date, $content);
        $this->comments[] = $comment;
        return $comment;

    }

    /**
     * @param Comment $comment
     */
    public function save(Comment $comment)
    {
        $this->comments[] = $comment;
        $this->saveMethodCalled = true;
    }

    /**
     * @param string $id
     */
    public function deleteComment(string $id)
    {
        foreach ($this->comments as $key => $comment) {
            if ($comment->id() === $id) {
                unset($this->comments[$key]);
            }
        }
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array
    {
        foreach ($this->comments as $comment) {
            if ($comment->reportId() === $reportId) {
                $newComments[] = $comment;
            }
        }
        return $newComments;
    }

    /**
     * @param string $id
     * @return Comment|null
     */
    public function findCommentById(string $id): Comment
    {
        foreach ($this->comments as $comment) {
            if ($comment->id() === $id) {
                return $comment;
            }
        }
        return null;
    }
}
