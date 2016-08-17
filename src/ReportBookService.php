<?php

namespace Jimdo\Reports;

class ReportBookService
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
    public function createReport(Trainee $trainee, string $content): Report
    {
        return new Report($trainee, $content);
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $this->reportRepository->save($report);
    }

    /**
     * @return Report[]
     */
    public function findAll()
    {
        return $this->reportRepository->findAll();
    }

    /**
     * @param Trainee $trainee
     * @return Report[]
     */
    public function findByTrainee(Trainee $trainee)
    {
        return $this->reportRepository->findByTrainee($trainee);
    }

    /**
     * @param Report $report
     */
    public function delete(Report $report)
    {
        $this->reportRepository->delete($report);
    }

    /**
     * @param Report $report
     */
    public function requestApproval(Report $report)
    {
        $report->requestApproval();
    }

    /**
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        return $this->reportRepository->findByStatus($status);
    }
}
