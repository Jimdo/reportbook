<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class ReportMongoRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);
        $reportBook = $client->reportBook;
        $reports = $reportBook->reports;

        $repository = new ReportMongoRepository($client, new Serializer());

        $traineeId = uniqid();
        $expectedContent = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';

        $report = $repository->create($traineeId, $expectedContent, $date, $calendarWeek);

        $serializedReport = $reports->findOne(['id' => $report->id()]);
        $unserializedReport = $repository->serializer->unserializeReport($serializedReport->getArrayCopy());

        $this->assertEquals($report->id(), $unserializedReport->id());

        $repository->delete($report);
    }

    /**
     * @test
     */
    public function itShouldFindAllReports()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);
        $reportBook = $client->reportBook;
        $reports = $reportBook->reports;

        $repository = new ReportMongoRepository($client, new Serializer());

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
    public function itShouldDeleteReport()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);

        $repository = new ReportMongoRepository($client, new Serializer());

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
