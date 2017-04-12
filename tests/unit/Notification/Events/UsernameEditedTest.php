<?php

namespace Jimdo\Reports\Notification\Events;

use PHPUnit\Framework\TestCase;

class UsernameEditedTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveType()
    {
        $payload = [];
        $event = new UsernameEdited($payload);

        $this->assertEquals('usernameEdited', $event->type());
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

        $event = new UsernameEdited($payload);

        $this->assertEquals($payload, $event->payload());
    }
}
