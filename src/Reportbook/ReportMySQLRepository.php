<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;
use Jimdo\Reports\Reportbook\Report;

class ReportMySQLRepository implements ReportRepository
{
    /** @var PDO */
    private $dbHandler;

    /** @var Serializer */
    private $serializer;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /** @var string */
    private $table;

    /**
     * @param PDO $dbHandler
     * @param Serializer $serializer
     * @param ApplicationConfig $applicationConfig
     */
    public function __construct(\PDO $dbHandler, Serializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
        $this->serializer = $serializer;
        $this->dbHandler = $dbHandler;
        $this->table = 'report';
    }

    /**
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $calendarYear
     * @param string $category
     * @return Report
     */
    public function create(
        TraineeId $traineeId,
        string $content,
        string $date,
        string $calendarWeek,
        string $calendarYear,
        string $category
    ): Report {
        $report = new Report($traineeId, $content, $date, $calendarWeek, $calendarYear, uniqid(), $category);

        $this->save($report);

        return $report;
    }

    /**
     * @param string $id
     * @return Report
     */
    public function findById(string $id)
    {
        return $this->serializer->unserializeReport(
            $this->dbHandler->query(
                "SELECT * FROM {$this->table} WHERE id = '{$id}'"
            )->fetchAll()[0]
        );
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $reports = [];
        foreach ($this->dbHandler->query("SELECT * FROM {$this->table}")->fetchAll() as $pdoObject) {
            $reports[] = $this->serializer->unserializeReport($pdoObject);
        }
        return $this->sortReportsByCalendarWeekAndYear($reports);
    }

    /**
     * @param string $traineeId
     * @return array
     */
    public function findByTraineeId(string $traineeId): array
    {
        $reports = [];
        foreach ($this->dbHandler->query("SELECT * FROM {$this->table} WHERE traineeId = '{$traineeId}'")->fetchAll() as $pdoObject) {
            $reports[] = $this->serializer->unserializeReport($pdoObject);
        }
        return $reports;
    }

    /**
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        $reports = [];
        foreach ($this->dbHandler->query("SELECT * FROM {$this->table} WHERE status = '{$status}'")->fetchAll() as $pdoObject) {
            $reports[] = $this->serializer->unserializeReport($pdoObject);
        }
        return $reports;
    }

    /**
     * @param string $text
     * @return array
     */
    public function findReportsByString(string $text): array
    {
        if ($text === '') {
            return $this->findAll();
        }

        $reports = [];

        if (is_numeric($text)) {
            foreach ($this->dbHandler->query("SELECT * FROM {$this->table} WHERE calendarWeek = '{$text}'")->fetchAll() as $pdoObject) {
                $reports[] = $this->serializer->unserializeReport($pdoObject);
            }
        } else {
            foreach ($this->dbHandler->query("SELECT * FROM {$this->table} WHERE content LIKE '%{$text}%'")->fetchAll() as $pdoObject) {
                $reports[] = $this->serializer->unserializeReport($pdoObject);
            }
        }
        return $reports;
    }

    /**
     * @param Report $report
     */
    public function delete(Report $report)
    {
        $this->dbHandler->exec("DELETE FROM report WHERE id = '{$report->id()}'");
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $this->dbHandler->exec("INSERT INTO {$this->table} (
            id, content, date, calendarWeek, calendarYear, status, category, traineeId
        ) VALUES (
            '{$report->id()}', '{$report->content()}', '{$report->date()}', '{$report->calendarWeek()}', '{$report->calendarYear()}', '{$report->status()}', '{$report->category()}', '{$report->traineeId()}'
        )");
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
