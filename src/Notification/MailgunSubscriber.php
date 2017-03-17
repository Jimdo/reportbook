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

        $this->mailgunClient = new Mailgun($this->appConfig->mailgunKey);
        $this->domain = $this->appConfig->mailgunDomain;
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
                $message =  "Hallo {$event->payload()['username']}, \n\n" .
                            "Dein Bericht wurde erstellt. \n\n" .
                            "Kalenderwoche: {$event->payload()['calendarWeek']} \n" .
                            "Bericht: \n\n" .
                            "{$event->payload()['content']} \n\n" .
                            "Online Berichtsheft";
                $this->sendMail("{$event->payload()['username']} <{$event->payload()['email']}>", $event->payload()['emailSubject'], $message);
                break;
            case 'approvalRequested':
                $message =  "Hallo {$event->payload()['username']}, \n\n" .
                            "Dein Bericht wurde erfolgreich eingereicht. \n\n" .
                            "Online Berichtsheft";
                $this->sendMail("{$event->payload()['username']} <{$event->payload()['email']}>", $event->payload()['emailSubject'], $message);
                break;
            case 'reportApproved':
                $message =  "Hallo {$event->payload()['username']}, \n\n" .
                            "Dein Bericht wurde erfolgreich von einem Ausbilder genehmigt. \n\n" .
                            "Online Berichtsheft";
                $this->sendMail("{$event->payload()['username']} <{$event->payload()['email']}>", $event->payload()['emailSubject'], $message);
                break;
            case 'reportDisapproved':
                $message =  "Hallo {$event->payload()['username']}, \n\n" .
                            "Dein Bericht wurde leider von einem Ausbilder abgelehnt. \n" .
                            "Überprüfe bitte nochmal Dein Bericht von der Kalenderwoche: {$event->payload()['calendarWeek']}.\n\n" .
                            "Online Berichtsheft";
                $this->sendMail("{$event->payload()['username']} <{$event->payload()['email']}>", $event->payload()['emailSubject'], $message);
                break;
            case 'commentCreated':
                if ($event->payload()['traineeId'] !== $event->payload()['userId']) {
                    $message =  "Hallo {$event->payload()['username']}, \n\n" .
                                "Dein Bericht von der Kalenderwoche {$event->payload()['calendarWeek']} wurde kommentiert. \n\n" .
                                "Online Berichtsheft";
                    $this->sendMail("{$event->payload()['username']} <{$event->payload()['email']}>", $event->payload()['emailSubject'], $message);
                }
                break;
            case 'roleApproved':
                    $message =  "Hallo {$event->payload()['username']}, \n\n" .
                                "Dein Zugang zum Online Berichtsheft wurde freigeschaltet. \n\n" .
                                "Online Berichtsheft";
                    $this->sendMail("{$event->payload()['username']} <{$event->payload()['email']}>", $event->payload()['emailSubject'], $message);
                break;
            case 'roleDisapproved':
                    $message =  "Hallo {$event->payload()['username']}, \n\n" .
                                "Dein Zugang zum Online Berichtsheft wurde abgelehnt. \n\n" .
                                "Online Berichtsheft";
                    $this->sendMail("{$event->payload()['username']} <{$event->payload()['email']}>", $event->payload()['emailSubject'], $message);
                break;
            case 'passwordEdited':
                    $message =  "Hallo {$event->payload()['username']}, \n\n" .
                                "Dein Passwort wurde erfolgreich geändert. \n\n" .
                                "Online Berichtsheft";
                    $this->sendMail("{$event->payload()['username']} <{$event->payload()['email']}>", $event->payload()['emailSubject'], $message);
                break;
        }
    }

    /**
     * @param string $emailTo
     * @param string $emailSubject
     * @param string $emailText
     */
    protected function sendMail(string $emailTo, string $emailSubject, string $emailText)
    {
        if (getenv('APPLICATION_ENV') === 'prod') {
            $this->mailgunClient->sendMessage("$this->domain", [
                'from'    => 'Online Berichtsheft <postmaster@berichtsheft.io>',
                'to'      => $emailTo,
                'subject' => $emailSubject,
                'text'    => $emailText
            ]);
        }
    }
}
