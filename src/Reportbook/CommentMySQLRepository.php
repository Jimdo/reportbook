<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\MySQLSerializer;

class CommentMySQLRepository implements CommentRepository
{
    /** @var PDO */
    private $dbHandler;

    /** @var Serializer */
    private $serializer;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /** @var string */
    private $table;

    /**
     * @param PDO $dbHandler
     * @param Serializer $serializer
     * @param ApplicationConfig $applicationConfig
     */
    public function __construct(\PDO $dbHandler, MySQLSerializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
        $this->serializer = $serializer;
        $this->dbHandler = $dbHandler;
        $this->table = 'comment';
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
        $comment = new Comment(uniqId(), $reportId, $userId, $date, $content);

        $this->save($comment);

        return $comment;
    }

    /**
     * @param Comment $comment
     * @throws CommentRepositoryException
     */
    public function save(Comment $comment)
    {
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$comment->id()]);
        $foundComment = $sth->fetchAll();

        if ($this->checkIfCommentFound($foundComment)) {
            $sql = "UPDATE $this->table SET id=:id, content=:content, date=:date, status=:status, userId=:userId, reportId=:reportId
                    WHERE id = :id";
        } else {
            $sql = "INSERT INTO $this->table (
                id, content, date, status, userId, reportId
            ) VALUES (
                :id, :content, :date, :status, :userId, :reportId
            )";
        }

        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([
            ':id' => $comment->id(),
            ':content' => $comment->content(),
            ':date' => $comment->date(),
            ':status' => $comment->status(),
            ':userId' => $comment->userId(),
            ':reportId' => $comment->reportId()
        ]);
    }

    /**
     * @param string $id
     * @throws CommentRepositoryException
     */
    public function deleteComment(string $id)
    {
        $sql = "DELETE FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$id]);
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array
    {
        $sql = "SELECT * FROM $this->table WHERE reportId = ?";
        $sth = $this->dbHandler->prepare($sql);

        $sth->execute([$reportId]);

        $comments = [];
        foreach ($sth->fetchAll() as $commentArr) {
            $comments[] = $this->serializer->unserializeComment($commentArr);
        }
        return $comments;
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findCommentsByUserId(string $userId): array
    {
        $sql = "SELECT * FROM $this->table WHERE userId = ?";
        $sth = $this->dbHandler->prepare($sql);

        $sth->execute([$userId]);

        $comments = [];
        foreach ($sth->fetchAll() as $commentArr) {
            $comments[] = $this->serializer->unserializeComment($commentArr);
        }
        return $comments;
    }

    /**
     * @param string $id
     * @return Comment|null
     */
    public function findCommentById(string $id)
    {
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$id]);

        $comment = $sth->fetchAll();
        if ($this->checkIfCommentFound($comment)) {
            return $this->serializer->unserializeComment($comment[0]);
        }
    }

    /**
     * @param array $userArr
     * @return bool
     */
    private function checkIfCommentFound(array $commentArr): bool
    {
        if (array_key_exists('0', $commentArr)) {
            return true;
        }
        return false;
    }
}
