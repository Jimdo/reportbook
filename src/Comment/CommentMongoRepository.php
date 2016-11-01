<?php

namespace Jimdo\Reports\Comment;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class CommentMongoRepository implements CommentRepository
{
    /** @var Serializer */
    public $serializer;

    /** @var MongoDB\Client */
    private $client;

    /** @var MongoDB\Database */
    private $reportbook;

    /** @var MongoDB\Collection */
    private $comments;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /**
     * @param Serializer $serializer
     * @param Client $client
     */
    public function __construct(\MongoDB\Client $client, Serializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
        $this->serializer = $serializer;
        $this->client = $client;
        $this->reportbook = $this->client->selectDatabase($this->applicationConfig->mongoDatabase);
        $this->comments = $this->reportbook->comments;
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
        $comment = new Comment(uniqid(), $reportId, $userId, $date, $content);

        $this->save($comment);

        return $comment;
    }

    /**
     * @param Comment $comment
     * @throws CommentRepositoryException
     */
    public function save(Comment $comment)
    {
        $this->deleteComment($comment->id());
        $this->comments->insertOne($this->serializer->serializeComment($comment));
    }

    /**
     * @param string $id
     * @throws CommentRepositoryException
     */
    public function deleteComment(string $id)
    {
        $this->comments->deleteOne(['id' => $id]);
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array
    {
        $foundComments = [];
        foreach ($this->comments->find() as $comment) {
            $comment = $this->serializer->unserializeComment($comment->getArrayCopy());
            if ($reportId === $comment->reportId()) {
                $foundComments[] = $comment;
            }
        }
        return $foundComments;
    }

    /**
     * @param string $id
     * @return Comment|null
     */
    public function findCommentById(string $id): Comment
    {
        foreach ($this->comments->find() as $comment) {
            $comment = $this->serializer->unserializeComment($comment->getArrayCopy());
            if ($id === $comment->id()) {
                return $comment;
            }
        }
    }
}
