<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class NotificationMongoRepositoryTest extends TestCase
{
    /** @var Client */
    private $client;

    /** @var Collection */
    private $notifications;

    /** @var ApplicationConfig */
    private $appConfig;

    private $repository;

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

        $this->notifications = $reportbook->notifications;

        $this->notifications->deleteMany([]);

        $this->repository = new NotificationMongoRepository($this->client, new Serializer(), $this->appConfig);
    }

    /**
     * @test
     */
    public function itShouldCreateNotification()
    {
        $title = 'title';
        $description = 'description';
        $userId = uniqid();
        $reportId = uniqid();

        $notification = $this->repository->create($title, $description, $userId, $reportId);

        $this->assertEquals($notification->userId(), $userId);
    }

    /**
     * @test
     */
    public function itShouldDeleteNotification()
    {
        $title = 'title';
        $description = 'description';
        $userId = uniqid();
        $reportId = uniqid();

        $notification1 = $this->repository->create($title, $description, $userId, $reportId);
        $notification2 = $this->repository->create($title, $description, $userId, $reportId);
        $this->repository->delete($notification1);

        $foundNotifications = $this->repository->findByUserId($userId);

        $this->assertCount(1, $foundNotifications);
    }

    /**
     * @test
     */
    public function itShouldFindNotificationById()
    {
        $title = 'title';
        $description = 'description';
        $userId = uniqid();
        $reportId = uniqid();

        $notification = $this->repository->create($title, $description, $userId, $reportId);

        $foundNotification = $this->repository->findById($notification->id());

        $this->assertEquals($notification, $foundNotification);
    }

    /**
     * @test
     */
    public function itShouldFindNotificationsByStatus()
    {
        $title = 'title';
        $description = 'description';
        $userId = uniqid();
        $reportId = uniqid();

        $this->repository->create($title, $description, $userId, $reportId);
        $this->repository->create($title, $description, $userId, $reportId);

        $foundNotifications = $this->repository->findByStatus(Notification::STATUS_NEW);

        $this->assertCount(2, $foundNotifications);
    }

    /**
     * @test
     */
    public function itShouldFindNotificationsByUserId()
    {
        $title = 'title';
        $description = 'description';
        $userId = uniqid();
        $reportId = uniqid();

        $this->repository->create($title, $description, $userId, $reportId);
        $this->repository->create($title, $description, $userId, $reportId);

        $foundNotifications = $this->repository->findByUserId($userId);

        $this->assertCount(2, $foundNotifications);
    }
}
