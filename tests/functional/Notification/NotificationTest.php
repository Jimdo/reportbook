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

        $notification = new Notification($title, $description);

        $this->assertEquals($title, $notification->title());
        $this->assertEquals($description, $notification->description());
     }

    /**
     * @test
     */
     public function itShouldHaveStatusNewAfterCreation()
     {
        $title = 'title';
        $description = 'description';

        $notification = new Notification($title, $description);

        $this->assertEquals(Notification::STATUS_NEW, $notification->status());
     }
}
