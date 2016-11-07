<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

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
