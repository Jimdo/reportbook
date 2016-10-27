<?php

namespace Jimdo\Reports\Comment;

interface CommentRepository
{
    /**
     * @param string $reportId
     * @param string $userId
     * @param string $date
     * @param string $content
     * @return Comment
     */
    public function createComment(string $reportId, string $userId, string $date, string $content): Comment;

    /**
     * @param string $newContent
     */
    public function editComment(string $newContent);

    /**
     * @param Comment $comment
     * @throws CommentRepositoryException
     */
    public function save(Comment $comment);

    /**
     * @param Comment $deleteComment
     * @throws CommentRepositoryException
     */
    public function deleteComment(Comment $deleteComment);

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array;
}
