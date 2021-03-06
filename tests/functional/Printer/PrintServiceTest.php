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

    /** @var string */
    private $outputDir;

    protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $this->userId = uniqid();

        $profile = new Profile($this->userId, 'Max', 'Mustermann');
        $profileService = \Mockery::mock('Jimdo\Reports\Profile\ProfileService');
        $profileService->shouldReceive('createProfile')->andReturn($profile);
        $profileService->shouldReceive('findProfileByUserId')->andReturn($profile);

        $reports = [];
        $content = "<ul><li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li><li>8</li><li>9</li></ul>";
        for ($i = 1; $i < 20; $i++) {
            $report = new Report(new TraineeId($this->userId), $content, date('d-m-Y'), $i, '2017', uniqid(), 'SCHOOL');
            $report->approve();
            $reports[] = $report;
        }

        $reportService = \Mockery::mock('Jimdo\Reports\Reportbook\ReportbookService');
        $reportService->shouldReceive('findByTraineeId')->andReturn($reports);

        $this->profileService = $profileService;
        $this->reportService = $reportService;

        $this->pdfParser = new \Smalot\PdfParser\Parser();

        $this->printService = new PrintService($this->profileService, $this->reportService, $this->appConfig);
        $this->outputDir = '/../../..' . $this->appConfig->printerOutput;
    }

    /**
     * @test
     */
    public function itShouldPrintCover()
    {
        $profile = $this->profileService->createProfile($this->userId, 'Max', 'Mustermann');

        $this->printService->printCover($this->userId, 'Herr', 'Hauke', 'Stange', 'Stresemannstraße 375', '22761 Hamburg');

        $pdf = $this->pdfParser->parseFile($this->outputDir . '/Deckblatt.pdf');

        $pages = $pdf->getPages();

        $this->assertEquals(0, strpos($pages[0]->getText(), 'Berichtsheft'));
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

        $pdf = $this->pdfParser->parseFile($this->outputDir . '/Berichte.pdf');

        $pages = $pdf->getPages();

        $this->assertEquals(232, strpos($pages[0]->getText(), '9'));
        $this->assertCount(8, $pages);
    }

    /**
     * @test
     */
    public function itShouldPrintReportbook()
    {
        $profile = $this->profileService->createProfile($this->userId, 'Hase', 'Igel');
        $profile->editJobTitle('Fachinformatiker Anwendungsentwicklung');

        $this->printService->printReportbook($this->userId, 'Herr', 'Hase', 'Igel', 'Stresemannstraße 375', '22761 Hamburg');

        $pdf = $this->pdfParser->parseFile($this->outputDir . '/Berichtsheft.pdf');

        $pages = $pdf->getPages();

        $this->assertCount(20, $pages);
    }

    /**
     * @test
     */
    public function itShouldReturnCorrectTrainingYearOfReport()
    {
        $dateOfReport = '12.08.2016';
        $startOfTraining = '2016-08-01';

        $trainingYear = $this->printService->getTrainingYearOfReport($dateOfReport, $startOfTraining);

        $this->assertEquals(1, $trainingYear);

        $dateOfReport = '21.05.2018';
        $startOfTraining = '2016-08-01';

        $trainingYear = $this->printService->getTrainingYearOfReport($dateOfReport, $startOfTraining);

        $this->assertEquals(2, $trainingYear);

        $dateOfReport = '12.08.2018';
        $startOfTraining = '2016-08-01';

        $trainingYear = $this->printService->getTrainingYearOfReport($dateOfReport, $startOfTraining);

        $this->assertEquals(3, $trainingYear);
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
}
