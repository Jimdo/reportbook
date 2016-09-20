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
    }
}
