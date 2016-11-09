<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event;
use Jimdo\Reports\Web\ApplicationConfig;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogUdpHandler;

class PapertrailSubscriber implements Subscriber
{
    /** @var array */
    private $validEventTypes;

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
