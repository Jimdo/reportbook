<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class BrowserNotificationServiceTest extends TestCase
{
    /** @var BrowserNotificationService */
    private $service;

    /** @var BroserNotificationRepository */
    private $repository;

    /** @var Client */
    private $client;

    /** @var Collection */
    private $notifications;

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

        $this->notifications = $reportbook->notifications;

        $this->notifications->deleteMany([]);

        $this->repository = new BrowserNotificationMongoRepository($this->client, new Serializer(), $this->appConfig);
        $this->service = new BrowserNotificationService($this->repository);
    }

    /**
     * @test
     */
    public function itShouldCreateNotification()
    {
        $title = 'title';
        $description = 'description';
        $reportId = uniqid();
        $userId = uniqid();

        $notification = $this->service->create($title, $description, $userId, $reportId);

        $this->assertEquals($reportId, $notification->reportId());
        $this->assertEquals($userId, $notification->userId());
        $this->assertEquals($title, $notification->title());
        $this->assertEquals($description, $notification->description());
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

        $notification1 = $this->service->create($title, $description, $userId, $reportId);
        $notification2 = $this->service->create($title, $description, $userId, $reportId);
        $this->service->delete($notification1);

        $foundNotifications = $this->service->findByUserId($userId);

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

        $notification = $this->service->create($title, $description, $userId, $reportId);

        $foundNotification = $this->service->findById($notification->id());

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

        $this->service->create($title, $description, $userId, $reportId);
        $this->service->create($title, $description, $userId, $reportId);

        $foundNotifications = $this->service->findByStatus(BrowserNotification::STATUS_NEW);

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

        $this->service->create($title, $description, $userId, $reportId);
        $this->service->create($title, $description, $userId, $reportId);

        $foundNotifications = $this->service->findByUserId($userId);

        $this->assertCount(2, $foundNotifications);
    }

    /**
     * @test
     */
    public function itShouldChangeStatusToSeen()
    {
        $title = 'title';
        $description = 'description';
        $userId = uniqid();
        $reportId = uniqid();

        $notification = $this->service->create($title, $description, $userId, $reportId);

        $this->service->seen($notification->id());

        $foundNotification = $this->service->findById($notification->id());

        $this->assertEquals(BrowserNotification::STATUS_SEEN, $foundNotification->status());
        $this->assertEquals($notification->time(), $foundNotification->time());
    }
}
