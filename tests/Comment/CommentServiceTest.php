<?php

namespace Jimdo\Reports\Comment;

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
        $this->service = new CommentService($this->repository, $this->appConfig->defaultProfile);
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
}
