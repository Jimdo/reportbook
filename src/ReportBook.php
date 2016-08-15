<?php

namespace Jimdo\Reports;

class ReportBook
{
    /**
     * @param string $content
     * @return Report
     */
    public function createReport(string $content): Report
    {
        return new Report($content);
    }
}
