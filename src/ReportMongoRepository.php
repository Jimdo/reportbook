<?php

namespace Jimdo\Reports;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

class ReportMongoRepository implements ReportRepository
{
    /** @var Serializer */
    public $serializer;

    /** @var MongoDB\Client */
    private $client;

    /** @var MongoDB\Database */
    private $reportbook;

    /** @var MongoDB\Collection */
    private $reports;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /**
     * @param Serializer $serializer
     * @param Client $client
     */
    public function __construct(\MongoDB\Client $client, Serializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->serializer = $serializer;
        $this->client = $client;
        $this->reportbook = $this->client->reportbook;
        $this->reports = $this->reportbook->reports;
        $this->applicationConfig = new ApplicationConfig(__DIR__ . '/../config.yml');
    }

    /**
     * @param string $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @return Report
     */
    public function create(string $traineeId, string $content, string $date, string $calendarWeek): Report
    {
        $report = new Report($traineeId, $content, $date, $calendarWeek, uniqid());

        $this->save($report);

        return $report;
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        if ($this->findById($report->id()) !== null) {

            $this->delete($report);
            $this->reports->insertOne($this->serializer->serializeReport($report));

        } else {

            $this->reports->insertOne($this->serializer->serializeReport($report));
        }
    }

    /**
     * @return Report[]
     */
    public function findAll(): array
    {
        $foundReports = [];
        foreach( $this->reports->find() as $report )
        {
            $foundReports [] = $this->serializer->unserializeReport($report->getArrayCopy());
        }
        return $foundReports;
    }

    /**
     * @param string $traineeId
     * @return Report[]
     */
    public function findByTraineeId(string $traineeId): array
    {
        $foundReports = $this->findAll();
        $reports = [];

        foreach($foundReports as $report) {

            if ($report->traineeId() === $traineeId) {
                $reports[] = $report;
            }

        }
        return $reports;
    }

    /**
     * @param Report $report
     */
    public function delete(Report $report)
    {
        $this->reports->deleteOne(['id' => $report->id()]);
    }

    /**
     * @param string $status
     * @return Report[]
     */
    public function findByStatus(string $status): array
    {
        $foundReports = $this->findAll();
        $reports = [];

        foreach($foundReports as $report) {

            if ($report->status() === $status) {
                $reports[] = $report;
            }

        }
        return $reports;
    }

    /**
     * @param string $id
     * @return Report
     */
    public function findById(string $id)
    {
        foreach($this->findAll() as $report) {

            if ($report->id() === $id) {
                return $report;
            }

        }
    }
}
