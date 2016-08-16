<?php

namespace Jimdo\Reports;

interface ReportRepository
{
    /**
     * @param Report $report
     */
    public function save(Report $report);

    /**
     * @return Report[]
     */
    public function findAll(): array;
}
