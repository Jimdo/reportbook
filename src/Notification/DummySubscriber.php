<?php

namespace Jimdo\Reports\Notification;

class DummySubscriber implements Subscriber
{
    /** @var array */
    public $validEventTypes = [];

    /**
     * @param array $eventTypes
     */
    public function __construct(array $eventTypes)
    {
        $this->validEventTypes = $eventTypes;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function isResponsibleFor(Event $event): bool
    {

    }

    /**
     * @param Event $event
     */
    public function notify(Event $event)
    {

    }
}
