<?php

namespace Jimdo\Reports\Notification\Events;

use PHPUnit\Framework\TestCase;

class JobTitleEditedTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveType()
    {
        $payload = [];
        $event = new JobTitleEdited($payload);

        $this->assertEquals('jobTitleEdited', $event->type());
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

        $event = new JobTitleEdited($payload);

        $this->assertEquals($payload, $event->payload());
    }
}
