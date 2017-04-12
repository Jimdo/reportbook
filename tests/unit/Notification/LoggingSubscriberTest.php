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

        $loggingSubscriber = new LoggingSubscriber($eventTypes, new ApplicationConfig(__DIR__ . '/../../../config.yml'));

        $this->assertTrue($loggingSubscriber->isResponsibleFor($event));
    }

    /**
     * @test
     */
    public function itShouldWriteToFileSystem()
    {
        $eventTypes = [
            'reportCreated',
            'reportEdited'
        ];

        $payload = [
            'userId' => uniqid(),
            'reportId' => uniqid(),
            'content' => 'some content'
        ];

        $reportCreated = new Events\ReportCreated($payload);
        $loggingSubscriber = new LoggingSubscriber($eventTypes, new ApplicationConfig(__DIR__ . '/../../../config.yml'));

        $loggingSubscriber->notify($reportCreated);

        $fileContent = file_get_contents($loggingSubscriber->appConfig->logPath);

        $this->assertInternalType('string', $fileContent);
    }

    protected function tearDown()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');

        if (file_exists($appConfig->logPath)) {
            unlink($appConfig->logPath);
        }
    }
}
