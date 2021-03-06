<?php

namespace Jimdo\Reports\functional\Reportbook;

use Jimdo\Reports\Reportbook\ReportRepository;
use Jimdo\Reports\Reportbook\Report;
use Jimdo\Reports\Reportbook\TraineeId;

class ReportFakeRepository implements ReportRepository
{
    /** @var Report[] */
    public $reports = [];

    /** @var Report */
    public $newReport;

    /** @var bool */
    public $saveMethodCalled = false;

    /**
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $calendarYear
     * @param string $category
     * @return Report
     */
    public function create(TraineeId $traineeId, string $content, string $date, string $calendarWeek, string $calendarYear, string $category): Report
    {
        $report = new Report($traineeId, $content, $date, $calendarWeek, $calendarYear, uniqid(), $category);
        $this->reports[] = $report;
        return $report;
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $this->reports[] = $report;
        $this->saveMethodCalled = true;
    }

    /**
     * Return all the reports
     *
     * @return Report[]
     */
    public function findAll(): array
    {
        return $this->reports;
    }

    /**
     * Return reports by trainee
     *
     * @param string $traineeId
     * @return Report[]
     */
    public function findByTraineeId(string $traineeId): array
    {
        $results = [];
        foreach ($this->reports as $report) {
            if ($report->traineeId() === $traineeId) {
                $results[] = $report;
            }
        }
        return $results;
    }

    /**
     * @param string $id
     * @return Report
     */
    public function findById(string $id): Report
    {
        foreach ($this->reports as $report) {
            if ($report->id() === $id) {
                return $report;
            }
        }
        return null;
    }

    /**
     * Return reports by status
     *
     * @return Report[]
     */
    public function findByStatus(string $status): array
    {
        $results = [];
        foreach ($this->reports as $report) {
            if ($report->status() === $status) {
                $results[] = $report;
            }
        }
        return $results;
    }

    /**
     * @param string $text
     * @return Report[]
     */
    public function findReportsByString(string $text): array
    {
        $foundReports = $this->findAll();
        $reports = [];

        foreach ($foundReports as $report) {
            if (strpos($report->content(), $text) !== false) {
                $reports[] = $report;
            }
        }
        return $reports;
    }

    /**
     * @param Report $deleteReport
     */
    public function delete(Report $deleteReport)
    {
        foreach ($this->reports as $key => $report) {
            if ($report === $deleteReport) {
                unset($this->reports[$key]);
            }
        }
    }
}
