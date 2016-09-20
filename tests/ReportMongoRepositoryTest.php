<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class ReportMongoRepositoryTest extends TestCase
{
    /** @var Client $client */
    private $client;

    /** @var Collection $users */
    private $reports;

    protected function setUp()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $this->client = new \MongoDB\Client($uri);
        $reportBook = $this->client->reportBook;
        $this->reports = $reportBook->reports;

        $this->reports->deleteMany([]);
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer());

        $traineeId = uniqid();
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $report = $repository->create($traineeId, $expectedContent, $date, $calendarWeek);

        $serializedReport = $this->reports->findOne(['id' => $report->id()]);
        $unserializedReport = $repository->serializer->unserializeReport($serializedReport->getArrayCopy());

        $this->assertEquals($report->id(), $unserializedReport->id());

        $repository->delete($report);
    }

    /**
     * @test
     */
    public function itShouldFindAllReports()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer());

        $traineeId = uniqid();
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

        $repository->delete($report1);
        $repository->delete($report2);
        $repository->delete($report3);
    }

    /**
     * @test
     */
    public function itShouldFindReportsByTraineeId()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer());

        $traineeId1 = '12345';
        $traineeId2 = '54321';
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $report1 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek);
        $report2 = $repository->create($traineeId1, $expectedContent, $date, $calendarWeek);

        $report3 = $repository->create($traineeId2, $expectedContent, $date, $calendarWeek);

        $foundReports = $repository->findByTraineeId($traineeId1);
        $this->assertCount(2, $foundReports);

        $foundReports = $repository->findByTraineeId($traineeId2);
        $this->assertCount(1, $foundReports);

        $repository->delete($report1);
        $repository->delete($report2);
        $repository->delete($report3);
    }

    /**
     * @test
     */
    public function itShouldDeleteReport()
    {
        $repository = new ReportMongoRepository($this->client, new Serializer());

        $traineeId = uniqid();
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
