<?php

namespace Jimdo\Reports\Notification;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

class PapertrailSubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveValidEventTypesAndShouldBeResponsibleFor()
    {
        $event = new Events\DummyEvent([]);

        $papertrailSubscriber = new PapertrailSubscriber(['dummyEvent'], new ApplicationConfig(__DIR__ . '/../../config.yml'));

        $this->assertTrue($papertrailSubscriber->isResponsibleFor($event));
    }

    /**
     * @notest
     */
    public function itShouldSendToPapertrail()
    {
        $event = new Events\DummyEvent([]);

        $papertrailSubscriber = new PapertrailSubscriber(['dummyEvent'], new ApplicationConfig(__DIR__ . '/../../config.yml'));
        $papertrailSubscriber->notify($event);
    }
}
