<?php

namespace Jimdo\Reports\Reportbook;

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
     * @param Comment $comment
     * @throws CommentRepositoryException
     */
    public function save(Comment $comment);

    /**
     * @param string $id
     * @throws CommentRepositoryException
     */
    public function deleteComment(string $id);

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array;

    /**
     * @param string $id
     * @return Comment|null
     */
    public function findCommentById(string $id): Comment;
}
