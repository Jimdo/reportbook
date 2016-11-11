<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event;
use Jimdo\Reports\Web\ApplicationConfig;
use Mailgun\Mailgun;

class MailgunSubscriber implements Subscriber
{
    /** @var array */
    private $validEventTypes;

    /** @var Mailgun */
    private $mailgunClient;

    /** @var string */
    private $domain;

    /** @var ApplicationConfig */
    public $appConfig;

    /**
     * @param array $eventTypes
     * @param ApplicationConfig $appConfig
     */
    public function __construct(array $eventTypes, ApplicationConfig $appConfig)
    {
        $this->validEventTypes = $eventTypes;
        $this->appConfig = $appConfig;

        $this->mailgunClient = new Mailgun('key-1beb90fc6968263d6ba2cf95fc6ca8bb');
        $this->domain = "sandboxd9e5c670536f4828a8ded4df88519471.mailgun.org";
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function isResponsibleFor(Event $event): bool
    {
        return in_array($event->type(), $this->validEventTypes);
    }
}
