<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\MongoSerializer as MongoSerializer;

class CommentMongoRepositoryTest extends TestCase
{
    /** @var Client */
    private $client;

    /** @var Collection */
    private $comments;

    /** @var ApplicationConfig */
    private $appConfig;

    protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $this->appConfig->mongoUsername
            , $this->appConfig->mongoPassword
            , $this->appConfig->mongoHost
            , $this->appConfig->mongoPort
            , $this->appConfig->mongoDatabase
        );

        $this->client = new \MongoDB\Client($uri);

        $reportbook = $this->client->selectDatabase($this->appConfig->mongoDatabase);

        $this->comments = $reportbook->comments;

        $this->comments->deleteMany([]);
    }

    /**
     * @test
     */
    public function itShouldCreateComment()
    {
        $repository = new CommentMongoRepository($this->client, new MongoSerializer(), $this->appConfig);

        $commentId = uniqid();
        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.20.20';
        $content = 'Inhalt';

        $comment = $repository->createComment($reportId, $userId, $date, $content);

        $this->assertEquals($reportId, $comment->reportId());
    }

    /**
     * @test
     */
    public function itShouldFindCommentsByReportId()
    {
        $repository = new CommentMongoRepository($this->client, new MongoSerializer(), $this->appConfig);

        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.20.20';
        $content = 'Inhalt';

        $comment1 = $repository->createComment($reportId, $userId, $date, $content);
        $comment2 = $repository->createComment($reportId, $userId, $date, $content);
        $comment3 = $repository->createComment($reportId, $userId, $date, $content);
        $comment4 = $repository->createComment($reportId, $userId, $date, $content);

        $comments = $repository->findCommentsByReportId($reportId);

        $this->assertCount(4, $comments);
    }

    /**
     * @test
     */
    public function itShouldSaveComment()
    {
        $repository = new CommentMongoRepository($this->client, new MongoSerializer(), $this->appConfig);

        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.20.20';
        $content = 'Inhalt';

        $comment = $repository->createComment($reportId, $userId, $date, $content);

        $comments = $repository->findCommentsByReportId($reportId);


        $this->assertCount(1, $comments);
    }

    /**
     * @test
     */
    public function itShouldDeleteComment()
    {
        $repository = new CommentMongoRepository($this->client, new MongoSerializer(), $this->appConfig);

        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.20.20';
        $content = 'Inhalt';

        $comment = $repository->createComment($reportId, $userId, $date, $content);
        $comments = $repository->findCommentsByReportId($reportId);
        $this->assertCount(1, $comments);

        $repository->deleteComment($comment->id());
        $comments = $repository->findCommentsByReportId($reportId);
        $this->assertCount(0, $comments);
    }

    /**
     * @test
     */
    public function itShouldFindCommentById()
    {
        $repository = new CommentMongoRepository($this->client, new MongoSerializer(), $this->appConfig);

        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.20.20';
        $content = 'Inhalt';

        $comment = $repository->createComment($reportId, $userId, $date, $content);

        $foundComment = $repository->findCommentById($comment->id());

        $this->assertEquals($comment->id(), $foundComment->id());
    }
}
