<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event as Event;

class NotificationService
{
    /** @var array */
    private $subscribers;

    /**
     * @param Subscriber $subscriber
     */
    public function register(Subscriber $subscriber)
    {
        $this->subscribers[] = $subscriber;
    }

    /**
     * @param Event $event
     */
    public function notify(Event $event)
    {
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->isResponsibleFor($event)) {
                $subscriber->notify($event);
            }
        }
    }
}
