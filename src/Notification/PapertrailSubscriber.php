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
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'reportEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'reportDeleted':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}, reportid={$event->payload()['reportId']}");
                break;
            case 'approvalRequested':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}, reportid={$event->payload()['reportId']}");
                break;
            case 'commentCreated':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}, reportid={$event->payload()['reportId']}");
                break;
            case 'commentEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}, reportid={$event->payload()['reportId']}");
                break;
            case 'commentDeleted':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}, reportid={$event->payload()['reportId']}");
                break;
            case 'companyEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'dateOfBirthEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'emailEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'forenameEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'gradeEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'imageEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'jobTitleEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'passwordEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'reportApproved':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}, reportid={$event->payload()['reportId']}");
                break;
            case 'reportDisapproved':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}, reportid={$event->payload()['reportId']}");
                break;
            case 'roleApproved':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'roleDisapproved':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'schoolEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'startOfTrainingEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'surnameEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'traineeRegistered':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'trainerRegistered':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'trainingYearEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'userAuthorized':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
            case 'usernameEdited':
                $this->sendToPaperTrail("event={$event->type()}, userid={$event->payload()['userId']}");
                break;
        }
    }

    /**
     * @param string $message
     * @param string $program
     */
    protected function sendToPaperTrail(string $message)
    {
        $program = $this->appConfig->papertrailSystem;
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        foreach(explode("\n", $message) as $line) {
          $syslog_message = "<22>" . date('M d H:i:s ') . $program . ' ' . 'web' . ': ' . $line;
          socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, $this->appConfig->papertrailHost, $this->appConfig->papertrailPort);
        }
        socket_close($sock);
    }
}
