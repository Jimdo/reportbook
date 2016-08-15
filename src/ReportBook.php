<?php

namespace Jimdo\Reports;

class ReportBook
{
    public function createReport(string $content): Report
    {
        return new Report();
    }
}
