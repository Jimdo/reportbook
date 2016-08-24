<?php

namespace Jimdo\Reports;

use Jimdo\Reports\Views\Report as ReadOnlyReport;

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
     * @param string $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @return \Jimdo\Reports\Views\Report
     */
    public function createReport(string $traineeId, string $content, string $date, string $calendarWeek): \Jimdo\Reports\Views\Report
    {
        $report = $this->reportRepository->create($traineeId, $content, $date, $calendarWeek);
        return new ReadOnlyReport($report);
    }

    /**
     * @param string $reportId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     */
    public function editReport(string $reportId, string $content, string $date, string $calendarWeek)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->edit($content, $date, $calendarWeek);
        $this->reportRepository->save($report);
    }

    /**
     * @return \Jimdo\Reports\Views\Report[]
     */
    public function findAll(): array
    {
        return $this->readOnlyReports(
            $this->reportRepository->findAll()
        );
    }

    /**
     * @param string $traineeId
     * @return \Jimdo\Reports\Views\Report[]
     */
    public function findByTraineeId(string $traineeId): array
    {
        return $this->readOnlyReports(
            $this->reportRepository->findByTraineeId($traineeId)
        );
    }

    /**
     * @param string $reportId
     */
    public function deleteReport(string $reportId)
    {
        $report = $this->reportRepository->findById($reportId);
        $this->reportRepository->delete($report);
    }

    /**
     * @param string $reportId
     */
    public function requestApproval(string $reportId)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->requestApproval();
        $this->reportRepository->save($report);
    }

    /**
     * @param string $reportId
     */
    public function approveReport(string $reportId)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->approve();
        $this->reportRepository->save($report);
    }

    /**
     * @param string $reportId
     */
    public function disapproveReport(string $reportId)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->disapprove();
        $this->reportRepository->save($report);
    }

    /**
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        return $this->reportRepository->findByStatus($status);
    }

    /**
     * @param string $reportId
     * @param string $traineeId
     * @return \Jimdo\Reports\Views\Report
     */
    public function findById(string $reportId, string $traineeId)
    {
        $report = $this->reportRepository->findById($reportId);
        $report = new ReadOnlyReport($report);
        if ($report->traineeId() === $traineeId) {
            return $report;
        }
        return null;
    }

    /**
     * @param Reports[] $reports
     * @return \Jimdo\Reports\Views\Report[]
     */
    private function readOnlyReports(array $reports): array
    {
        $readOnlyReports = [];
        foreach ($reports as $report) {
            $readOnlyReports[] = new ReadOnlyReport($report);
        }
        return $readOnlyReports;
    }
}
