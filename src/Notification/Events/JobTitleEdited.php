<?php

namespace Jimdo\Reports\Notification\Events;

class JobTitleEdited implements Event
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
        return 'jobTitleEdited';
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->payload;
    }
}
