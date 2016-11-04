<?php

namespace Jimdo\Reports\Notification;

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
