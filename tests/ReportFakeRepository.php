<?php

namespace Jimdo\Reports;

class ReportFakeRepository implements ReportRepository
{
    /** @var Report[] */
    public $reports = [];

    /** @var Report */
    public $newReport;

    /** @var bool */
    public $saveMethodCalled = false;

    /**
     * @param string $traineeId
     * @param string $content
     * @return Report
     */
    public function create(string $traineeId, string $content): Report
    {
        $report = new Report($traineeId, $content);
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
