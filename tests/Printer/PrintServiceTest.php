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

    /** @var pdfParser */
    private $pdfParser;

    /** @var ApplicationConfig */
    private $appConfig;

    /** @var userId */
    private $userId;

    protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');
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

        $this->pdfParser = new \Smalot\PdfParser\Parser();

        $this->printService = new PrintService($this->profileService, $this->reportService, $this->appConfig);

        if (!file_exists($this->appConfig->printerOutput)) {
            mkdir($this->appConfig->printerOutput);
        }
    }

    protected function tearDown()
    {
        $this->deleteRecursive($this->appConfig->printerOutput);
        rmdir($this->appConfig->printerOutput);
    }

    /**
     * @test
     */
    public function itShouldPrintCover()
    {
        $profile = $this->profileService->createProfile($this->userId, 'Max', 'Mustermann');

        $this->printService->printCover($this->userId, 'Herr', 'Hauke', 'Stange', 'Stresemannstraße 375', '22761 Hamburg');

        $pdf = $this->pdfParser->parseFile($this->appConfig->printerOutput . '/Deckblatt.pdf');

        $pages  = $pdf->getPages();

        $this->assertEquals(0, strpos($pages[0]->getText(), 'Berichtsheft'));
        $this->assertTrue(file_exists($this->appConfig->printerOutput . '/Deckblatt.pdf'));
        $this->assertCount(1, $pages);
    }

    /**
     * @test
     */
    public function itShouldPrintReports()
    {
        $profile = $this->profileService->createProfile($this->userId, 'Max', 'Mustermann');
        $profile->editJobTitle('Fachinformatiker Anwendungsentwicklung');

        $this->printService->printReports($this->userId, '2', '2017', '3', '2017');

        $this->assertTrue(file_exists('app/pdf/Berichte.pdf'));
    }

    /**
     * @test
     */
    public function itShouldCountLines()
    {
        $text = "<ul><li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li></ul>";
        $countedLines = $this->printService->countLines($text);
        $this->assertEquals(7, $countedLines);

        $text = "<p>1</p><p>2</p><p>3</p><p>4</p><p>5</p><p>6</p><p>7<br>8</p>";
        $countedLines = $this->printService->countLines($text);
        $this->assertEquals(8, $countedLines);

        $text = "<ol><li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li><li>8</li></ol>";
        $countedLines = $this->printService->countLines($text);
        $this->assertEquals(8, $countedLines);
    }

    private function deleteRecursive($input)
    {
        if (is_file($input)) {
            unlink($input);
            return;
        }

        if (is_dir($input)) {
            foreach (scandir($input) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $file = join('/', [$input, $file]);
                if (is_file($file)) {
                    unlink($file);
                    continue;
                }

                $this->deleteRecursive($file);
            }
        }
    }
}
