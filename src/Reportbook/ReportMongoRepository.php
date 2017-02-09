<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

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
     * @param ApplicationConfig $applicationConfig
     */
    public function __construct(\MongoDB\Client $client, Serializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
        $this->serializer = $serializer;
        $this->client = $client;
        $this->reportbook = $this->client->selectDatabase($this->applicationConfig->mongoDatabase);
        $this->reports = $this->reportbook->reports;
    }

    /**
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $category
     * @return Report
     */
    public function create(TraineeId $traineeId, string $content, string $date, string $calendarWeek, string $calendarYear, string $category): Report
    {
        $report = new Report($traineeId, $content, $date, $calendarWeek, $calendarYear, uniqid(), $category);

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

        foreach ($this->reports->find() as $report) {
            $foundReports [] = $this->serializer->unserializeReport($report->getArrayCopy());
        }
        $this->sortReportsByCalendarWeek($foundReports);
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

        foreach ($foundReports as $report) {
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

        foreach ($foundReports as $report) {
            if ($report->status() === $status) {
                $reports[] = $report;
            }
        }
        return $reports;
    }

    /**
     * @param string $text
     * @return Report[]
     */
    public function findReportsByString(string $text): array
    {
        $foundReports = $this->findAll();

        if ($text === '') {
            return $foundReports;
        }

        $reports = [];

        if (is_numeric($text)) {
             foreach ($this->reports->find(['calendarWeek' => $text]) as $report) {
                 $reports [] = $this->serializer->unserializeReport($report->getArrayCopy());
             }
        } else {
            foreach ($this->reports->find(array('content' => new \MongoDB\BSON\Regex($text, 'i'))) as $report) {
                $reports [] = $this->serializer->unserializeReport($report->getArrayCopy());
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
        foreach ($this->findAll() as $report) {
            if ($report->id() === $id) {
                return $report;
            }
        }
    }

    /**
     * @param array $array
     */
    public function sortReportsByCalendarWeek(&$array)
    {
        $direction = SORT_DESC;

        $reference_array = [];
        $reports = [];

        foreach ($array as $report) {
            $report = $this->serializer->serializeReport($report);
            $reports[] = $report;
        }

        $array = $reports;

        foreach ($array as $key => $row) {
            $reference_array[$key] = $row['calendarWeek'];
        }

        array_multisort($reference_array, $direction, $array);

        $newReports = [];
        foreach ($array as $report) {
            $newReports[] = $this->serializer->unserializeReport($report);
        }

        $array = $newReports;
    }

    /**
     * @param array $array
     * @return array
     */
    public function sortReportsByCalendarWeekAndYear(array $aReports): array
    {
        $years = [];
        $yearsWithReports = [];
        $sortedReports = [];

        foreach ($aReports as $report) {

          if (!in_array($report->calendarYear(), $years)) {
              $years[] = $report->calendarYear();
          }

          foreach ($years as $year) {
              if ($report->calendarYear() === $year) {
                  $yearsWithReports[$year][] = $report;
              }
          }
        }

        foreach ($yearsWithReports as $year => $reports) {
          $this->sortReportsByCalendarWeek($reports);
          $sortedReports[$year] = $reports;
        }

        krsort($sortedReports);

        $returnArr = [];
        foreach ($sortedReports as $sortedReport) {
          $returnArr = array_merge($returnArr, $sortedReport);
        }

        return $returnArr;
    }
}
