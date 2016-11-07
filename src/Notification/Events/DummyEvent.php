<?php

namespace Jimdo\Reports\Notification\Events;

class DummyEvent implements Event
{
    /** @var array */
    private $payload;

    /**
     * @param Comment $comment
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
        return 'dummyEvent';
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->payload;
    }
}
