<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Views\Report as ReadOnlyReport;
use Jimdo\Reports\Reportbook\Report;
use Jimdo\Reports\Reportbook\CommentService;
use Jimdo\Reports\Serializer;
use Jimdo\Reports\ErrorCodeStore;
use Jimdo\Reports\User\Role;

class ReportbookService
{
    /** @var ReportRepository */
    public $reportRepository;

    /** @var CommentService */
    private $commentService;

    /** @var Serializer */
    private $serializer;

    /**
     * @param ReportRepository $reportRepository
     * @param CommentService $commentService
     * @param Serializer $serializer
     */
    public function __construct(ReportRepository $reportRepository, CommentService $commentService, Serializer $serializer)
    {
        $this->reportRepository = $reportRepository;
        $this->commentService = $commentService;
        $this->serializer = $serializer;
    }

    /**
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $calendarYear
     * @param string $category
     * @return \Jimdo\Reports\Views\Report
     * @throws ReportFileRepositoryException
     */
    public function createReport(
        TraineeId $traineeId,
        string $content,
        string $calendarWeek,
        string $calendarYear,
        string $category
    ): \Jimdo\Reports\Views\Report {
        $report = $this->reportRepository->create($traineeId, $content, date('d.m.Y'), $calendarWeek, $calendarYear, $category);

        return new ReadOnlyReport($report);
    }

    /**
     * @param string $reportId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $calendarYear
     * @param string $category
     * @throws ReportFileRepositoryException
     */
    public function editReport(string $reportId, string $content, string $calendarWeek, string $calendarYear, string $category)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->edit($content, $report->date(), $calendarWeek, $calendarYear, $category);
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
     * @param string $text
     * @return array
     */
    public function findReportsByString(string $text, string $userId, string $role): array
    {
        $foundReports = $this->reportRepository->findReportsByString($text);
        $returnReports = [];

        if ($role === Role::TRAINER) {
            foreach ($foundReports as $report) {
                if ($report->status() === Report::STATUS_APPROVAL_REQUESTED ||
                    $report->status() === Report::STATUS_APPROVED ||
                    $report->status() === Report::STATUS_DISAPPROVED ||
                    $report->status() === Report::STATUS_REVISED) {
                    $returnReports[] = $report;
                }
            }
        } elseif ($role === Role::TRAINEE) {
            foreach ($foundReports as $report) {
                if ($userId === $report->traineeId()) {
                    $returnReports[] = $report;
                }
            }
        } elseif ($role === Role::ADMIN) {
            $returnReports = $foundReports;
        }
        return $returnReports;
    }


    /**
     * @param string $currentReportId
     * @param string $traineeId
     * @return \Jimdo\Reports\Report || null
     */
    public function findNextReport(string $currentReportId, string $traineeId)
    {
        $currentReport = $this->findById($currentReportId, $traineeId);

        $reports =  $this->findByTraineeId($traineeId);

        $nextReportWeekAndYear = $this->calculateNextReportWeekAndYear(
            intval($currentReport->calendarWeek()),
            intval($currentReport->calendarYear())
        );

        foreach ($reports as $report) {
            if ($report->calendarWeek() == $nextReportWeekAndYear[0] and $report->calendarYear() == $nextReportWeekAndYear[1]) {
                if ($report->status() === Report::STATUS_APPROVAL_REQUESTED) {
                    return $report;
                }
            }
        }
    }

    /**
     * @param string $calendarWeek
     * @param string $calendarYear
     * @return array
     */
    public function calculateNextReportWeekAndYear(int $calendarWeek, int $calendarYear): array {
        $nextCalendarWeek = $calendarWeek + 1;
        $nextCalendarYear = $calendarYear;

        if ($nextCalendarWeek == 53) {
            $nextCalendarWeek = 1;
            $nextCalendarYear += 1;
        }

        return [$nextCalendarWeek, $nextCalendarYear];
    }

    /**
     * @param string $reportId
     * @param string $traineeId
     * @return \Jimdo\Reports\Views\Report
     * @throws ReportFileRepositoryException
     */
    public function findById(string $reportId, string $traineeId, bool $isAdmin = false)
    {
        $report = $this->reportRepository->findById($reportId);
        if ($report === null) {
            return null;
        }
        $report = new ReadOnlyReport($report);
        if ($report->traineeId() === $traineeId || $isAdmin) {
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
                'You are not allowed to edit this comment!',
                ErrorCodeStore::ERR_EDIT_COMMENT_DENIED
            );
        }
    }

    /**
     * @param string $commentId
     * @throws ReportbookServiceException
     */
    public function deleteComment(string $commentId, string $userId)
    {
        $comment = $this->findCommentById($commentId);
        if ($userId === $comment->userId()) {
            $this->commentService->deleteComment($commentId);
        } else {
            throw new ReportbookServiceException(
                'You are not allowed to delete this comment!',
                ErrorCodeStore::ERR_DELETE_COMMENT_DENIED
            );
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
     * @param string $userId
     * @return array
     */
    public function findCommentsByUserId(string $userId): array
    {
        return $this->commentService->findCommentsByUserId($userId);
    }

    /**
     * @param string $key
     * @param array $array
     */
    public function sortArrayDescending(string $key, array &$array)
    {
        $direction = SORT_DESC;
        $value = $key;
        $reference_array = [];
        $reports = [];
        foreach ($array as $report) {
            $report = $this->serializer->serializeReport($report);
            $reports[] = $report;
        }
        $array = $reports;
        foreach ($array as $key => $row) {
            $reference_array[$key] = $row[$value];
        }
        array_multisort($reference_array, $direction, $array);
        $newReports = [];
        foreach ($array as $report) {
            $newReports[] = $this->serializer->unserializeReport($report);
        }
        $array = $newReports;
    }

    /**
     * @param array $status
     * @param array $array
     */
    public function sortReportsByStatus(array $status, array &$reports)
    {
        $sortedReports = [];
        foreach ($reports as $report) {
            switch ($report->status()) {
                case $status[0]:
                    $sortedReports[$status[0]][] = $report;
                    break;

                case $status[1]:
                    $sortedReports[$status[1]][] = $report;
                    break;

                case $status[2]:
                    $sortedReports[$status[2]][] = $report;
                    break;

                case $status[3]:
                    $sortedReports[$status[3]][] = $report;
                    break;

                case $status[4]:
                    $sortedReports[$status[4]][] = $report;
                    break;

                case $status[5]:
                    $sortedReports[$status[5]][] = $report;
                    break;
            }
        }

        $returnReports = [];

        for ($i=0; $i < count($status); $i++) {
            if (array_key_exists($status[$i], $sortedReports)) {
                $returnReports = array_merge($returnReports, $sortedReports[$status[$i]]);
            }
        }
        $reports = $returnReports;
    }

    /**
     * @param array $reportArray
     */
    public function sortReportsByAmountOfComments(array &$reportArray)
    {
        $direction = SORT_DESC;

        $reference_array = [];
        $reports = [];

        $comments;

        foreach ($reportArray as $report) {
            $commentsOfReport[] = count($this->findCommentsByReportId($report->id()));
        }

        foreach ($reportArray as $report) {
            $report = $this->serializer->serializeReport($report);
            $reports[] = $report;
        }

        $reportArray = $reports;

        array_multisort($commentsOfReport, $direction, $reportArray);

        $newReports = [];
        foreach ($reportArray as $report) {
            $newReports[] = $this->serializer->unserializeReport($report);
        }

        $reportArray = $newReports;
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
     * @param array $reports
     * @return array
     */
    public function sortReportsByCalendarWeekAndYear(array $reports): array
    {
        return $this->reportRepository->sortReportsByCalendarWeekAndYear($reports);
    }

    /**
     * @param array $array
     */
    private function sortCommentsByDate(array &$array)
    {
        $direction = SORT_ASC;

        $reference_array = [];
        $comments = [];

        foreach ($array as $comment) {
            $comment = $this->serializer->serializeComment($comment);
            $comments[] = $comment;
        }

        $array = $comments;

        foreach ($array as $key => $row) {
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
