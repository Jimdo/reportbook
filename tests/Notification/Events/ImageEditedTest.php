<?php

namespace Jimdo\Reports\Notification\Events;

use PHPUnit\Framework\TestCase;

class ImageEditedTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveType()
    {
        $payload = [];
        $event = new ImageEdited($payload);

        $this->assertEquals('imageEdited', $event->type());
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

        $event = new ImageEdited($payload);

        $this->assertEquals($payload, $event->payload());
    }
}
