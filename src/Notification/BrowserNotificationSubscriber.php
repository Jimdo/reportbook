<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Serializer;

class BrowserNotificationSubscriber implements Subscriber
{
    /** @var array */
    private $validEventTypes;

    /** @var ApplicationConfig */
    public $appConfig;

    /** @var NotificationMongoRepository */
    private $repository;
    /**
     * @param array $eventTypes
     * @param ApplicationConfig $appConfig
     */
    public function __construct(array $eventTypes, ApplicationConfig $appConfig)
    {
        $this->validEventTypes = $eventTypes;
        $this->appConfig = $appConfig;

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $this->appConfig->mongoUsername
            , $this->appConfig->mongoPassword
            , $this->appConfig->mongoHost
            , $this->appConfig->mongoPort
            , $this->appConfig->mongoDatabase
        );

        $this->client = new \MongoDB\Client($uri);

        $reportbook = $this->client->selectDatabase($this->appConfig->mongoDatabase);

        $this->notifications = $reportbook->notifications;

        $this->repository = new BrowserNotificationMongoRepository($this->client, new Serializer(), $this->appConfig);
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
        $userId = $event->payload()['userId'];
        $reportId = $event->payload()['reportId'];
        $calendarweek = "{$event->payload()['calendarWeek']}/{$event->payload()['calendarYear']}";

        switch ($event->type()) {
            case 'approvalRequested':
                $this->addNotification("Bericht genehmigt", "Dein Bericht für die Kalenderwoche $calendarweek wurde eingereicht.", $userId, $reportId);
                break;
            case 'commentCreated':
                if ($event->payload()['traineeId'] !== $event->payload()['userId']) {
                    $this->addNotification("Bericht kommentiert", "Dein Bericht für die Kalenderwoche $calendarweek wurde kommentiert.", $userId, $reportId);
                }
                break;;
            case 'emailEdited':
                $this->addNotification("Email geändert", "Deine E-Mail wurde erfolgreich zu {$event->payload()['email']} geändert.", $userId, $reportId);
                break;;
            case 'passwordEdited':
                $this->addNotification("Passwort geändert", "Deine Passwort wurde erfolgreich geändert.", $userId, $reportId);
                break;
            case 'reportApproved':
                $this->addNotification("Bericht genehmigt", "Dein Bericht für die Kalenderwoche $calendarweek wurde genehmight.", $userId, $reportId);
                break;
            case 'reportDisapproved':
                $this->addNotification("Bericht abgelehnt", "Dein Bericht für die Kalenderwoche $calendarweek wurde abgelehnt.", $userId, $reportId);
                break;
            case 'roleApproved':
                $this->addNotification("Zugang freigeschaltet", "Herzlich willkommen bei berichtsheft.io", $userId, $reportId);
                break;
        }
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     */
    private function addNotification(string $title, string $description, string $userId, string $reportId)
    {
        $this->repository->create($title, $description, $userId, $reportId);
    }
}
