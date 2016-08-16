<?php

namespace Jimdo\Reports;

class ReportFakeRepository implements ReportRepository
{
    /** @var Report */
    public $report;

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $this->report = $report;
    }
}
