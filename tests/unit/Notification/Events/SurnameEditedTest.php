<?php

namespace Jimdo\Reports\Notification\Events;

use PHPUnit\Framework\TestCase;

class SurnameEditedTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveType()
    {
        $payload = [];
        $event = new SurnameEdited($payload);

        $this->assertEquals('surnameEdited', $event->type());
    }

    /**
     * @test
     */
    public function itShouldHavePayload()
    {
        $payload = [
            'userId' => 'afhoafdo',
            'calendarWeek' => 'nfsdk',
            'content' => 'dsbds'
        ];

        $event = new SurnameEdited($payload);

        $this->assertEquals($payload, $event->payload());
    }
}
