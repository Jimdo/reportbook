<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Views\Report as ReadOnlyReport;
use Jimdo\Reports\Reportbook\CommentService as CommentService;
use Jimdo\Reports\Serializer as Serializer;

class ReportbookService
{
    const ERR_EDIT_COMMENT_DENIED = 11;
    const ERR_DELETE_COMMENT_DENIED = 12;

    /** @var ReportRepository */
    private $reportRepository;

    /** @var CommentService */
    private $commentService;

    /** @var Serializer */
    private $serializer;

    /**
     * @param ReportRepository $reportRepository
     */
    public function __construct(ReportRepository $reportRepository, CommentService $commentService)
    {
        $this->reportRepository = $reportRepository;
        $this->commentService = $commentService;
        $this->serializer = new Serializer();
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
     * @throws ReportbookServiceException
     * @return Comment
     */
    public function editComment(string $id, string $newContent, string $userId): Comment
    {
        $comment = $this->findCommentById($id);
        if ($userId === $comment->userId()) {
            return $this->commentService->editComment($id, $newContent);
        } else {
            throw new ReportbookServiceException(
                'You are not allowed to edit this Comment!' . "\n",
                self::ERR_EDIT_COMMENT_DENIED
            );
        }
    }

    /**
     * @param string $commentId
     */
    public function deleteComment(string $commentId, string $userId)
    {
        $comment = $this->findCommentById($commentId);
        if ($userId === $comment->userId()) {
            $this->commentService->deleteComment($commentId);
        }
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array
    {
        $comments = $this->commentService->findCommentsByReportId($reportId);
        $this->sortCommentsByDate($comments);

        return $comments;
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

    /**
     * @param array $array
     */
    private function sortCommentsByDate(&$array) {
        $direction = SORT_ASC;

        $reference_array = [];
        $comments = [];

        foreach ($array as $comment) {
            $comment = $this->serializer->serializeComment($comment);
            $comments[] = $comment;
        }

        $array = $comments;

        foreach($array as $key => $row) {
            $reference_array[$key] = $row['date'];
        }

        array_multisort($reference_array, $direction, $array);

        $newComments = [];
        foreach ($array as $comment) {
            $newComments[] = $this->serializer->unserializeComment($comment);
        }

        $array = $newComments;
    }
}
