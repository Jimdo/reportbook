<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;


class BrowserNotificationTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveVariables()
    {
        $title = 'Title';
        $description =  'A beautiful description';
        $userId =  uniqid();
        $reportId =  uniqid();

        $notification = new BrowserNotification($title, $description, $userId, $reportId);

        $this->assertEquals($title, $notification->title());
        $this->assertEquals($description, $notification->description());
        $this->assertEquals($userId, $notification->userId());
        $this->assertEquals($reportId, $notification->reportId());
    }

    /**
    * @test
    */
    public function itShouldHaveStatusNewAfterCreation()
    {
        $title = 'title';
        $description = 'description';
        $userId =  uniqid();
        $reportId =  uniqid();

        $notification = new BrowserNotification($title, $description, $userId, $reportId);

        $this->assertEquals(BrowserNotification::STATUS_NEW, $notification->status());
    }

    /**
    * @test
    */
    public function itShouldHaveStatusSeen()
    {
        $title = 'title';
        $description = 'description';
        $userId =  uniqid();
        $reportId =  uniqid();

        $notification = new BrowserNotification($title, $description, $userId, $reportId);

        $notification->seen();

        $this->assertEquals(BrowserNotification::STATUS_SEEN, $notification->status());
    }

    /**
    * @test
    */
    public function itShouldHaveTimestamp()
    {
        $title = 'title';
        $userId =  uniqid();
        $description = 'description';
        $reportId =  uniqid();

        $notification = new BrowserNotification($title, $description, $userId, $reportId);

        $this->assertInternalType("int", $notification->time());
    }
}
