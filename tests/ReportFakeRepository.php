<?php

namespace Jimdo\Reports;

class ReportFakeRepository implements ReportRepository
{
    /** @var Report[] */
    public $reports = [];

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $this->reports[] = $report;
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
     * Return all the reports
     *
     * @return Report[]
     */
    public function findByTrainee(Trainee $trainee): array
    {
        $results = [];
        foreach ($this->reports as $report) {
            if ($report->trainee() === $trainee) {
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
