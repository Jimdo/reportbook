<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class CommentServiceTest extends TestCase
{
    /** @var CommentService */
    private $service;

    /** @var ProfileMongoRepository */
    private $repository;

    /** @var Client */
    private $client;

    /** @var Collection */
    private $comments;

    /** @var ApplicationConfig */
    private $appConfig;

    protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

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

        $this->repository = new CommentMongoRepository($this->client, new Serializer(), $this->appConfig);
        $this->service = new CommentService($this->repository);
    }

    /**
     * @test
     */
    public function itShouldCreateComment()
    {
        $reportId = uniqid();
        $userId = uniqid();
        $date = date('d.m.Y');
        $content = 'Hallo';

        $comment = $this->service->createComment($reportId, $userId, $date, $content);

        $this->assertEquals($reportId, $comment->reportId());
        $this->assertEquals($userId, $comment->userId());
        $this->assertEquals($date, $comment->date());
        $this->assertEquals($content, $comment->content());
    }

    /**
     * @test
     */
    public function itShouldFindCommentsByReport()
    {
        $reportId = uniqid();
        $userId = uniqid();
        $date = date('d.m.Y');
        $content = 'Hallo';

        $comment = $this->service->createComment($reportId, $userId, $date, $content);
        $comment = $this->service->createComment($reportId, $userId, $date, $content);

        $comments = $this->service->findCommentsByReportId($reportId);

        $this->assertCount(2, $comments);
    }

    /**
     * @test
     */
    public function itShouldFindCommentById()
    {
        $reportId = uniqid();
        $userId = uniqid();
        $date = date('d.m.Y');
        $content = 'Hallo';

        $comment = $this->service->createComment($reportId, $userId, $date, $content);

        $foundComment = $this->service->findCommentById($comment->id());

        $this->assertEquals($comment->id(), $foundComment->id());
    }

    /**
     * @test
     */
    public function itShouldDeleteComment()
    {
        $reportId = uniqid();
        $userId = uniqid();
        $date = date('d.m.Y');
        $content = 'Hallo';

        $comment1 = $this->service->createComment($reportId, $userId, $date, $content);
        $comment2 = $this->service->createComment($reportId, $userId, $date, $content);

        $comments = $this->service->findCommentsByReportId($reportId);
        $this->assertCount(2, $comments);

        $this->service->deleteComment($comment1->id());

        $comments = $this->service->findCommentsByReportId($reportId);
        $this->assertCount(1, $comments);
    }

    /**
     * @test
     */
    public function itShouldEditComment()
    {
        $reportId = uniqid();
        $userId = uniqid();
        $date = date('d.m.Y');
        $content = 'Hallo';

        $comment = $this->service->createComment($reportId, $userId, $date, $content);

        $newContent = "Ciao";

        $comment = $this->service->editComment($comment->id(), $newContent);

        $this->assertEquals($newContent, $comment->content());
    }
}
