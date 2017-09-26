<?php

namespace Jimdo\Reports\functional\Notification;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\Notification\BrowserNotification as Notification;

class BrowserNotificationFakeRepositoryTest extends TestCase
{
    private $repository;


    protected function setUp()
    {
        $this->repository = new BrowserNotificationFakeRepository();
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