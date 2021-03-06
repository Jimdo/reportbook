<?php

namespace Jimdo\Reports\Notification\Events;

use PHPUnit\Framework\TestCase;

class PasswordEditedTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveType()
    {
        $payload = [];
        $event = new PasswordEdited($payload);

        $this->assertEquals('passwordEdited', $event->type());
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

        $event = new PasswordEdited($payload);

        $this->assertEquals($payload, $event->payload());
    }
}
