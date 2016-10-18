<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class ReportMongoRepositoryTest extends TestCase
{
    /** @var Client $client */
    private $client;

    /** @var Collection $users */
    private $reports;

    /** @var ApplicationConfig */
    private $appConfig;

    protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $this->appConfig->mongoUsername
            , $this->appConfig->mongoPassword
            , $this->appConfig->mongoHost
            , $this->appConfig->mongoPort
            , $this->appConfig->mongoDatabase
        );

        $this->client = new \MongoDB\Client($uri);

        $reportbook = $this->client->selectDatabase($this->appConfig->mongoDatabase);
        $this->reports = $reportbook->reports;

        $this->reports->deleteMany([]);
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer(), $this->appConfig);

        $traineeId = new TraineeId();
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $report = $repository->create($traineeId, $expectedContent, $date, $calendarWeek);

        $serializedReport = $this->reports->findOne(['id' => $report->id()]);
        $unserializedReport = $repository->serializer->unserializeReport($serializedReport->getArrayCopy());

        $this->assertEquals($report->id(), $unserializedReport->id());
    }

    /**
     * @test
     */
    public function itShouldFindAllReports()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer(), $this->appConfig);

        $traineeId = new TraineeId();
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $foundReports = $repository->findAll();
        $this->assertCount(0, $foundReports);

        $report1 = $repository->create($traineeId, $expectedContent, $date, $calendarWeek);
        $report2 = $repository->create($traineeId, $expectedContent, $date, $calendarWeek);
        $report3 = $repository->create($traineeId, $expectedContent, $date, $calendarWeek);

        $foundReports = $repository->findAll();
        $this->assertCount(3, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindReportsByTraineeId()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer(), $this->appConfig);

        $traineeId1 = new TraineeId();
        $traineeId2 = new TraineeId();
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $report1 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek);
        $report2 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek);

        $report3 = $repository->create($traineeId2, $expectedContent, $date, $calendarWeek);

        $foundReports = $repository->findByTraineeId($traineeId1->id());
        $this->assertCount(2, $foundReports);

        $foundReports = $repository->findByTraineeId($traineeId2->id());
        $this->assertCount(1, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindReportsByStatus()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer(), $this->appConfig);

        $traineeId1 = new TraineeId();
        $traineeId2 = new TraineeId();
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $report1 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek);
        $report2 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek);
        $report3 = $repository->create($traineeId2, $expectedContent, $date, $calendarWeek);

        $foundReports = $repository->findByStatus(Report::STATUS_NEW);
        $this->assertCount(3, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindReportById()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer(), $this->appConfig);

        $traineeId = new TraineeId();
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $report = $repository->create($traineeId, $expectedContent, $date, $calendarWeek);

        $foundReport = $repository->findById($report->id());

        $this->assertEquals($report->id(), $foundReport->id());
    }

    /**
     * @test
     */
    public function itShouldDeleteReport()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer(), $this->appConfig);

        $traineeId = new TraineeId();
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $report = $repository->create($traineeId, $expectedContent, $date, $calendarWeek);

        $foundReports = $repository->findAll();
        $this->assertCount(1, $foundReports);

        $repository->delete($report);

        $foundReports = $repository->findAll();
        $this->assertCount(0, $foundReports);
    }
}
