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
        $category = Category::SCHOOL;

        $report = $repository->create($traineeId, $expectedContent, $date, $calendarWeek , '2016', $category);

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
        $category = Category::SCHOOL;

        $foundReports = $repository->findAll();
        $this->assertCount(0, $foundReports);

        $report1 = $repository->create($traineeId, $expectedContent, $date, $calendarWeek , '2016', $category);
        $report2 = $repository->create($traineeId, $expectedContent, $date, $calendarWeek , '2016', $category);
        $report3 = $repository->create($traineeId, $expectedContent, $date, $calendarWeek , '2016', $category);

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
        $category = Category::SCHOOL;

        $report1 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek , '2016', $category);
        $report2 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek , '2016', $category);

        $report3 = $repository->create($traineeId2, $expectedContent, $date, $calendarWeek , '2016', $category);

        $foundReports = $repository->findByTraineeId($traineeId1->id());
        $this->assertCount(2, $foundReports);

        $foundReports = $repository->findByTraineeId($traineeId2->id());
        $this->assertCount(1, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindReportsByString()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer(), $this->appConfig);

        $traineeId = new TraineeId();
        $date = '10.10.10';
        $calendarWeek = '34';
        $category = Category::SCHOOL;

        $report1 = $repository->create($traineeId, 'some content', $date, $calendarWeek , '2016', $category);
        $report2 = $repository->create($traineeId, 'hello world', $date, $calendarWeek , '2016', $category);
        $report3 = $repository->create($traineeId, 'hello world', $date, $calendarWeek , '2016', $category);

        $foundReports = $repository->findReportsByString('world');

        $this->assertCount(2, $foundReports);
        $this->assertEquals('hello world', $foundReports[0]->content());
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
        $category = Category::COMPANY;

        $report1 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek , '2016', $category);
        $report2 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek , '2016', $category);
        $report3 = $repository->create($traineeId2, $expectedContent, $date, $calendarWeek , '2016', $category);

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
        $category = Category::SCHOOL;

        $report = $repository->create($traineeId, $expectedContent, $date, $calendarWeek , '2016', $category);

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
        $category = Category::SCHOOL;

        $report = $repository->create($traineeId, $expectedContent, $date, $calendarWeek , '2016', $category);

        $foundReports = $repository->findAll();
        $this->assertCount(1, $foundReports);

        $repository->delete($report);

        $foundReports = $repository->findAll();
        $this->assertCount(0, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldSortReportsByCalendarweek()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer(), $this->appConfig);

        $repository->create(new TraineeId(), 'some content', '15.5.11', '3', '2016', Category::SCHOOL);
        $repository->create(new TraineeId(), 'some content', '2.5.11', '1', '2016', Category::SCHOOL);
        $repository->create(new TraineeId(), 'some content', '11.11.11', '2', '2016', Category::SCHOOL);

        $foundReports = $repository->findAll();

        $this->assertEquals($foundReports[0]->calendarWeek(), '3');
        $this->assertEquals($foundReports[1]->calendarWeek(), '2');
        $this->assertEquals($foundReports[2]->calendarWeek(), '1');
    }
}
