<?php

namespace Jimdo\Reports\Notification;

class DummyEvent implements Event
{
    /** @var string */
    private $eventName;

    /**
     * @param Comment $comment
     */
    public function __construct(string $eventName)
    {
        $this->eventName = $eventName;
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
        return [
            'eventName' => $this->eventName,
        ];
    }
}
