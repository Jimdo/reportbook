<?php

namespace Jimdo\Reports\Notification;

class NotificationService
{
    private $subscribers;

    public function register(Subscriber $subscriber)
    {
        $this->subscribers[] = $subscriber;
    }
}
