<?php

namespace Jimdo\Reports\Notification\Events;

class ImageEdited implements Event
{
    /** @var array */
    private $payload;

    /**
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return 'imageEdited';
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->payload;
    }
}
