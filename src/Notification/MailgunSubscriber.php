<?php

namespace Jimdo\Reports\Notification;

use Jimdo\Reports\Notification\Events\Event;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\User\UserService;
use Jimdo\Reports\User\UserMongoRepository;
use Jimdo\Reports\Serializer;
use Mailgun\Mailgun;

class MailgunSubscriber implements Subscriber
{
    /** @var array */
    private $validEventTypes;

    /** @var Mailgun */
    private $mailgunClient;

    /** @var UserService */
    private $userService;

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

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $appConfig->mongoUsername
            , $appConfig->mongoPassword
            , $appConfig->mongoHost
            , $appConfig->mongoPort
            , $appConfig->mongoDatabase
        );

        $client = new \MongoDB\Client($uri);

        $userRepository = new UserMongoRepository($client, new Serializer(), $appConfig);
        $this->userService = new UserService($userRepository, $appConfig, new NotificationService());
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
                $user = $this->userService->findUserById($event->payload()['userId']);
                $message =  "Hallo {$user->username()}, \n\n" .
                            "Dein Bericht wurde erstellt. \n\n" .
                            "Kalenderwoche: {$event->payload()['calendarWeek']} \n" .
                            "Bericht: \n\n" .
                            "{$event->payload()['content']} \n\n" .
                            "Online Berichtsheft";
                $this->sendMail("{$user->username()} <{$user->email()}>", $event->payload()['emailSubject'], $message);
                break;
            case 'approvalRequested':
                $user = $this->userService->findUserById($event->payload()['userId']);
                $message =  "Hallo {$user->username()}, \n\n" .
                            "Dein Bericht wurde erfolgreich eingereicht. \n\n" .
                            "Online Berichtsheft";
                $this->sendMail("{$user->username()} <{$user->email()}>", $event->payload()['emailSubject'], $message);
                break;
            case 'reportApproved':
                $user = $this->userService->findUserById($event->payload()['userId']);
                $message =  "Hallo {$user->username()}, \n\n" .
                            "Dein Bericht wurde erfolgreich von einem Ausbilder genehmigt. \n\n" .
                            "Online Berichtsheft";
                $this->sendMail("{$user->username()} <{$user->email()}>", $event->payload()['emailSubject'], $message);
                break;
            case 'reportDisapproved':
                $user = $this->userService->findUserById($event->payload()['userId']);
                $message =  "Hallo {$user->username()}, \n\n" .
                            "Dein Bericht wurde leider von einem Ausbilder abgelehnt. \n" .
                            "Überprüfe bitte nochmal Dein Bericht von der Kalenderwoche: {$event->payload()['calendarWeek']}.\n\n" .
                            "Online Berichtsheft";
                $this->sendMail("{$user->username()} <{$user->email()}>", $event->payload()['emailSubject'], $message);
                break;
            case 'commentCreated':
                $user = $this->userService->findUserById($event->payload()['emailTo']);
                if ($user->id() !== $event->payload()['commentUserId']) {
                    $message =  "Hallo {$user->username()}, \n\n" .
                                "Dein Bericht von der Kalenderwoche {$event->payload()['calendarWeek']} wurde kommentiert. \n\n" .
                                "Online Berichtsheft";
                    $this->sendMail("{$user->username()} <{$user->email()}>", $event->payload()['emailSubject'], $message);
                }
                break;
            case 'roleApproved':
                $user = $this->userService->findUserById($event->payload()['userId']);
                    $message =  "Hallo {$user->username()}, \n\n" .
                                "Dein Zugang zum Online Berichtsheft wurde freigeschaltet. \n\n" .
                                "Online Berichtsheft";
                    $this->sendMail("{$user->username()} <{$user->email()}>", $event->payload()['emailSubject'], $message);
                break;
            case 'roleDisapproved':
                $user = $this->userService->findUserById($event->payload()['userId']);
                    $message =  "Hallo {$user->username()}, \n\n" .
                                "Dein Zugang zum Online Berichtsheft wurde abgelehnt. \n\n" .
                                "Online Berichtsheft";
                    $this->sendMail("{$user->username()} <{$user->email()}>", $event->payload()['emailSubject'], $message);
                break;
            case 'passwordEdited':
                $user = $this->userService->findUserById($event->payload()['userId']);
                    $message =  "Hallo {$user->username()}, \n\n" .
                                "Dein Passwort wurde erfolgreich geändert. \n\n" .
                                "Online Berichtsheft";
                    $this->sendMail("{$user->username()} <{$user->email()}>", $event->payload()['emailSubject'], $message);
                break;

        }
    }

    protected function sendMail(string $emailTo, string $emailSubject, string $emailText)
    {
        $result = $this->mailgunClient->sendMessage("$this->domain", [
            'from'    => 'Online Berichtsheft <postmaster@sandboxd9e5c670536f4828a8ded4df88519471.mailgun.org>',
            'to'      => $emailTo,
            'subject' => $emailSubject,
            'text'    => $emailText
        ]);
    }
}
