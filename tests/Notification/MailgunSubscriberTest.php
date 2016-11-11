<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

class MailgunSubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveValidEventTypesAndShouldBeResponsibleFor()
    {
        $event = new Events\DummyEvent([]);

        $mailgunSubscriber = new MailgunSubscriber(['dummyEvent'], new ApplicationConfig(__DIR__ . '/../../config.yml'));

        $this->assertTrue($mailgunSubscriber->isResponsibleFor($event));
    }

    /**
     * @notest
     */
    public function itShouldSendToMailgun()
    {
        $event = new Events\DummyEvent([]);

        $mailgunSubscriber = new MailgunSubscriber(['dummyEvent'], new ApplicationConfig(__DIR__ . '/../../config.yml'));
        $mailgunSubscriber->notify($event);
    }
}
