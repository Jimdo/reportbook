<?php

namespace Jimdo\Reports\Comment;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

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
    }

    /**
     * @test
     */
    public function itShouldCreateComment()
    {
        $repository = new CommentMongoRepository($this->client, new Serializer(), $this->appConfig);

        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.20.20';
        $content = 'Inhalt';

        $comment = $repository->createComment($reportId, $userId, $date, $content);

        $this->assertEquals($reportId, $comment->reportId());
    }
}
