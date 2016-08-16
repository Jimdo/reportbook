<?php

namespace Jimdo\Reports;

class ReportBook
{
    /** @var ReportRepository */
    private $reportRepository;

    /**
     * @param ReportRepository $reportRepository
     */
    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * @param string $content
     * @return Report
     */
    public function createReport(string $content): Report
    {
        return new Report($content);
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $this->reportRepository->save($report);
    }
}
