<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class CommentService
{
    /** @var string */
    private $repository;

    /**
     * @param CommentRepository $repository
     */
    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $reportId
     * @param string $userId
     * @param string $date
     * @param string $content
     * @return Comment
     */
    public function createComment(string $reportId, string $userId, string $date, string $content): Comment
    {
        return $this->repository->createComment($reportId, $userId, $date, $content);
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array
    {
        return $this->repository->findCommentsByReportId($reportId);
    }

    /**
     * @param string $id
     * @return Comment
     */
    public function findCommentById(string $id): Comment
    {
        return $this->repository->findCommentById($id);
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findCommentsByUserId(string $userId): array
    {
        return $this->repository->findCommentsByUserId($userId);
    }

    /**
     * @param string $id
     */
    public function deleteComment(string $id)
    {
        $this->repository->deleteComment($id);
    }

    /**
     * @param string $id
     * @return Comment
     */
    public function editComment(string $id, string $content): Comment
    {
        $comment = $this->findCommentById($id);
        $comment->editContent($content);
        $this->repository->save($comment);
        return $comment;
    }
}
