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

    /**
     * @param Event $event
     */
    public function notify(Event $event)
    {
        switch ($event->type()) {
            case 'reportCreated':
            case 'reportEdited':
            case 'companyEdited':
            case 'dateOfBirthEdited':
            case 'emailEdited':
            case 'forenameEdited':
            case 'gradeEdited':
            case 'imageEdited':
            case 'jobTitleEdited':
            case 'passwordEdited':
            case 'roleApproved':
            case 'roleDisapproved':
            case 'schoolEdited':
            case 'startOfTrainingEdited':
            case 'surnameEdited':
            case 'traineeRegistered':
            case 'trainerRegistered':
            case 'trainingYearEdited':
            case 'userAuthorized':
            case 'usernameEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'reportDeleted':
            case 'approvalRequested':
            case 'commentCreated':
            case 'commentEdited':
            case 'commentDeleted':
            case 'reportApproved':
            case 'reportDisapproved':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}, reportid={$event->payload()['reportId']}");
                break;
        }
    }

    /**
     * @param string $message
     */
    protected function sendToPaperTrail(string $message)
    {
        $program = $this->appConfig->papertrailSystem;
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        foreach (explode("\n", $message) as $line) {
            $syslog_message = "<22>" . date('M d H:i:s ') . $program . ' ' . 'web' . ': ' . $line;
            socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, $this->appConfig->papertrailHost, $this->appConfig->papertrailPort);
        }
        socket_close($sock);
    }
}
