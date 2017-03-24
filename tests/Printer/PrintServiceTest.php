<?php

namespace Jimdo\Reports\Printer;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Profile\Profile;
use Jimdo\Reports\Reportbook\Report;
use Jimdo\Reports\Reportbook\TraineeId;
use Jimdo\Reports\Web\ApplicationConfig;

class PrintServiceTest extends TestCase
{
    /** @var PDO */
    private $dbHandler;

    /** @var ProfileService */
    private $profileService;

    /** @var ReportbookService */
    private $reportService;

    /** @var PrintService */
    private $printService;

    /** @var userId */
    private $userId;

    protected function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');
        $this->userId = uniqid();

        $profile = new Profile($this->userId, 'Max', 'Mustermann');
        $profileService = \Mockery::mock('Jimdo\Reports\Profile\ProfileService');
        $profileService->shouldReceive('createProfile')->andReturn($profile);
        $profileService->shouldReceive('findProfileByUserId')->andReturn($profile);

        $reports = [];
        $content = "<p>Woche 11</p><ul><li>Fertigsetzung des Docker Umbaus zu Docker Compose</li><ul><li>Fehler bei Travis behoben in dem wir das mitgelieferte MySQL Setup von Travis für die Tests benutzen anstelle Bau eines MySQL Containers</li><li>für Docker Compose noch ein storage-reset im Makefile eingebaut, der die gesamten Service inklusive Volumes resettet</li><li>Den MySQL Dump für die Datenbank überarbeitet, da bei der Tabelle Report der Attribut UserId falsch war und nun TraineeId lautet, sowie die role eigentlich roleStatus heißt</li></ul><li>Tests für ReportMySQLRepository, CommentMySQLRepository, ProfileMySQLRepository und UserMySQLRepository gebaut - dabei die Syntax von PDO sowie MySQL kennengelernt</li><ul><li>Bei PDO die Methoden prepare und execute für das Verhalten der Repositories verwendet, damit eine Injection von außen vermieden werden kann</li><li>Den Serializer für die Benutzung von verschiedenen Datenbanken angepasst und private Methoden hinzugefügt, die z.B. für das unserializen eines Users richtig die Rolle zuordnet</li><ul><li>MongoSerializer und MySQLSerializer Interfaces erstellt und bei den Repositories anstelle des Objektes Serializer in den construct gepackt, damit nur die Methoden verwendet werden, die für das Repository gelten</li></ul></ul><li>Von Hauke eine kurze Erklärung über Joins bekommen und anhand vom Projekt direkt ein Beispiel in der Dev-Ebene durchgeführt</li><li>Die SoftMigration von gehashten Passwörtern weggenommen, da die Migration bereits durchlaufen ist und der Code überflüssig ist</li><li>Bei phpunit ein tag groups hinterlegt bei denem alle tests die <b>@group 'name' </b>haben bei dem Testdurchlauf ignoriert werden</li><li>Bei Mailgun nun berichtsheft.io als Domain hinterlegt und bei AWS die TXT und MX Records eingetragen - In der .env Datei dann noch die Domain richtig hinterlegt</li><ul><li>MailgunSubscriber Payload und Message überarbeitet und die Production Ebene als Fix freigeschaltet</li></ul></ul>";
        for ($i=1; $i < 20; $i++) {
            $reports[] = new Report(new TraineeId($this->userId), $content, date('d-m-Y'), $i, '2017', uniqid(), 'SCHOOL');
        }

        $reportService =  \Mockery::mock('Jimdo\Reports\Reportbook\ReportbookService');
        $reportService->shouldReceive('findByTraineeId')->andReturn($reports);

        $this->profileService = $profileService;
        $this->reportService = $reportService;

        $this->printService = new PrintService($this->profileService, $this->reportService, $appConfig);
    }
}
