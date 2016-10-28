<?php

namespace Jimdo\Reports\Comment;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class CommentService
{
    /** @var string */
    private $repository;

    /**
     * @param CommentMongoRepository $repository
     */
    public function __construct(CommentMongoRepository $repository)
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
    public function createComment(string $reportId, string $userId, string $date, string $content)
    {
        return $this->repository->createComment($reportId, $userId, $date, $content);
    }
}
