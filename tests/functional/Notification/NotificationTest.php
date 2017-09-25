<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;


class NotificationTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveVariables()
    {
        $title = 'Title';
        $description =  'A beautiful description';
        $reportId =  uniqid();

        $notification = new Notification($title, $description, $reportId);

        $this->assertEquals($title, $notification->title());
        $this->assertEquals($description, $notification->description());
        $this->assertEquals($reportId, $notification->reportId());
    }

    /**
    * @test
    */
    public function itShouldHaveStatusNewAfterCreation()
    {
        $title = 'title';
        $description = 'description';
        $reportId =  uniqid();

        $notification = new Notification($title, $description, $reportId);

        $this->assertEquals(Notification::STATUS_NEW, $notification->status());
    }

    /**
    * @test
    */
    public function itShouldHaveStatusSeen()
    {
        $title = 'title';
        $description = 'description';
        $reportId =  uniqid();

        $notification = new Notification($title, $description, $reportId);

        $notification->seen();

        $this->assertEquals(Notification::STATUS_SEEN, $notification->status());
    }

    /**
     * @test
     */
    public function itShouldHaveTimestamp()
    {
        $title = 'title';
        $description = 'description';
        $reportId =  uniqid();

        $notification = new Notification($title, $description, $reportId);

        $this->assertInternalType("int", $notification->time());
    }
}
