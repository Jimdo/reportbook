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
        switch ($event->type()) {
            case 'reportCreated':
                $this->writeLog("Report with id {$event->payload()['reportId']} by user {$event->payload()['userId']} created");
                break;
            case 'reportEdited':
                $this->writeLog("Report with id {$event->payload()['reportId']} by user {$event->payload()['userId']} edited");
                break;
            case 'reportDeleted':
                $this->writeLog("Report with id {$event->payload()['reportId']} by user {$event->payload()['userId']} deleted");
                break;
            case 'approvalRequested':
                $this->writeLog("Approval for Report with id {$event->payload()['reportId']} requested by user {$event->payload()['userId']}");
                break;
            case 'commentCreated':
                $this->writeLog("Comment with id {$event->payload()['reportId']} by user {$event->payload()['userId']} created");
                break;
            case 'commentEdited':
                $this->writeLog("Comment with id {$event->payload()['reportId']} by user {$event->payload()['userId']} edited");
                break;
            case 'commentDeleted':
                $this->writeLog("Comment with id {$event->payload()['reportId']} by user {$event->payload()['userId']} deleted");
                break;
            case 'companyEdited':
                $this->writeLog("Company of user {$event->payload()['userId']} edited");
                break;
            case 'dateOfBirthEdited':
                $this->writeLog("Date of birth of user {$event->payload()['userId']} edited");
                break;
            case 'emailEdited':
                $this->writeLog("Email of user {$event->payload()['userId']} edited");
                break;
            case 'forenameEdited':
                $this->writeLog("Forename of user {$event->payload()['userId']} edited");
                break;
            case 'gradeEdited':
                $this->writeLog("Grade of user {$event->payload()['userId']} edited");
                break;
            case 'imageEdited':
                $this->writeLog("Image of user {$event->payload()['userId']} edited");
                break;
            case 'jobTitleEdited':
                $this->writeLog("Job title of user {$event->payload()['userId']} edited");
                break;
            case 'passwordEdited':
                $this->writeLog("Password of user {$event->payload()['userId']} edited");
                break;
            case 'reportApproved':
                $this->writeLog("Report with id {$event->payload()['reportId']} approved by user {$event->payload()['userId']}");
                break;
            case 'reportDisapproved':
                $this->writeLog("Report with id {$event->payload()['reportId']} disapproved by user {$event->payload()['userId']}");
                break;
            case 'roleApproved':
                $this->writeLog("User {$event->payload()['userId']} approved");
                break;
            case 'roleDisapproved':
                $this->writeLog("User {$event->payload()['userId']} disapproved");
                break;
            case 'schoolEdited':
                $this->writeLog("School of user {$event->payload()['userId']} edited");
                break;
            case 'startOfTrainingEdited':
                $this->writeLog("Start of training of user {$event->payload()['userId']} edited");
                break;
            case 'surnameEdited':
                $this->writeLog("Surname of user {$event->payload()['userId']} edited");
                break;
            case 'traineeRegistered':
                $this->writeLog("Trainee with id {$event->payload()['userId']} registered");
                break;
            case 'trainerRegistered':
                $this->writeLog("Trainer with id {$event->payload()['userId']} registered");
                break;
            case 'trainingYearEdited':
                $this->writeLog("Training year of user {$event->payload()['userId']} edited");
                break;
            case 'userAuthorized':
                $this->writeLog("User with id {$event->payload()['userId']} logged in");
                break;
            case 'usernameEdited':
                $this->writeLog("Username of user {$event->payload()['userId']} edited");
                break;
        }
    }

    /**
     * @param string $logDescription
     */
    private function writeLog(string $logDescription)
    {
        file_put_contents($this->appConfig->logPath, '[' . date('d.m.Y H:i:s') . "] $logDescription\n", FILE_APPEND);
    }
}
