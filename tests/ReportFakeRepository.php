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
}
