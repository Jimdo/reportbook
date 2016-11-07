<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event as Event;

class LoggingSubscriber implements Subscriber
{
    /** @var array */
    private $validEventTypes;

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
        return in_array($event->type(), $this->validEventTypes);
    }

    /**
     * @param Event $event
     */
    public function notify(Event $event)
    {

    }
}
