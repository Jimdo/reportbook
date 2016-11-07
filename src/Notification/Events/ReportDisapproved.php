<?php

namespace Jimdo\Reports\Notification\Events;

class ReportDisapproved implements Event
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
        return 'reportDisapproved';
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->payload;
    }
}
