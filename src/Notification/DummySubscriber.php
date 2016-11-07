<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event as Event;

class DummySubscriber implements Subscriber
{
    /** @var array */
    public $validEventTypes = [];

    /** @var bool */
    public $notified = false;

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
        switch ($event->type()) {
            case 'dummyEvent':
                $this->notified = true;
                break;
        }
    }
}
