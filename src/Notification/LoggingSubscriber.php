<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event;
use Jimdo\Reports\Web\ApplicationConfig;

class LoggingSubscriber implements Subscriber
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

    /**
     * @param Event $event
     */
    public function notify(Event $event)
    {

    /**
     * @param string $logDescription
     */
    private function writeLog(string $logDescription)
    {
        file_put_contents($this->appConfig->logPath, date('d.m.Y H:i:s') . " | $logDescription\n", FILE_APPEND);
    }
}
