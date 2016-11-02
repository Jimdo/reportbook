<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Views\Report as ReadOnlyReport;
use Jimdo\Reports\Reportbook\CommentService as CommentService;

class ReportbookService
{
    /** @var ReportRepository */
    private $reportRepository;

    /** @var CommentService */
    private $commentService;

    /**
     * @param ReportRepository $reportRepository
     */
    public function __construct(ReportRepository $reportRepository, CommentService $commentService)
    {
        $this->reportRepository = $reportRepository;
        $this->commentService = $commentService;
    }

    /**
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @return \Jimdo\Reports\Views\Report
     * @throws ReportFileRepositoryException
     */
    public function createReport(
        TraineeId $traineeId,
        string $content,
        string $date,
        string $calendarWeek
    ): \Jimdo\Reports\Views\Report {
        $report = $this->reportRepository->create($traineeId, $content, $date, $calendarWeek);
        return new ReadOnlyReport($report);
    }

    /**
     * @param string $reportId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @throws ReportFileRepositoryException
     */
    public function editReport(string $reportId, string $content, string $date, string $calendarWeek)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->edit($content, $date, $calendarWeek);
        $this->reportRepository->save($report);
    }

    /**
     * @return \Jimdo\Reports\Views\Report[]
     * @throws ReportFileRepositoryException
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
     * @throws ReportFileRepositoryException
     */
    public function findByTraineeId(string $traineeId): array
    {
        return $this->readOnlyReports(
            $this->reportRepository->findByTraineeId($traineeId)
        );
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function deleteReport(string $reportId)
    {
        $report = $this->reportRepository->findById($reportId);
        $this->reportRepository->delete($report);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function requestApproval(string $reportId)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->requestApproval();
        $this->reportRepository->save($report);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function approveReport(string $reportId)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->approve();
        $this->reportRepository->save($report);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
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
     * @throws ReportFileRepositoryException
     */
    public function findByStatus(string $status): array
    {
        return $this->reportRepository->findByStatus($status);
    }

    /**
     * @param string $reportId
     * @param string $traineeId
     * @return \Jimdo\Reports\Views\Report
     * @throws ReportFileRepositoryException
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
     * @param string $reportId
     * @param string $userId
     * @param string $date
     * @param string $content
     * @return Comment
     */
    public function createComment(string $reportId, string $userId, string $date, string $content): Comment
    {
        return $this->commentService->createComment($reportId, $userId, $date, $content);
    }

    /**
     * @param string $id
     * @return Comment
     */
    public function editComment(string $id, string $newContent): Comment
    {
        return $this->commentService->editComment($id, $newContent);
    }

    /**
     * @param string $commentId
     */
    public function deleteComment(string $commentId)
    {
        $this->commentService->deleteComment($commentId);
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array
    {
        return $this->commentService->findCommentsByReportId($reportId);
    }

    /**
     * @param string $commentId
     * @return Comment
     */
    public function findCommentById(string $commentId): Comment
    {
        return $this->commentService->findCommentById($commentId);
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
