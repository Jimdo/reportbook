<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event as Event;

interface Subscriber
{
    /**
     * @param Event $event
     * @return bool
     */
    public function isResponsibleFor(Event $event): bool;

    /**
     * @param Event $event
     */
    public function notify(Event $event);
}
