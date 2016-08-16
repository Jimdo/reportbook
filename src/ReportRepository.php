<?php

namespace Jimdo\Reports;

interface ReportRepository
{
    public function save(Report $report);
}
