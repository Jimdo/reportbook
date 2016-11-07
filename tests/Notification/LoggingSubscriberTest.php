<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;

class LoggingSubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveValidEventTypesAndResponsibleFor()
    {
        $eventTypes = [
            'reportCreated',
            'reportEdited'
        ];

        $payload = [];
        $event = new Events\ReportCreated($payload);

        $loggingSubscriber = new LoggingSubscriber($eventTypes);

        $this->assertTrue($loggingSubscriber->isResponsibleFor($event));
    }
}
