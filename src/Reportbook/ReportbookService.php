<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Views\Report as ReadOnlyReport;
use Jimdo\Reports\Reportbook\CommentService as CommentService;
use Jimdo\Reports\Serializer as Serializer;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\Events as Events;
use Jimdo\Reports\User\Role;

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

    /** @var NotificaionService */
    private $notificationService;

    /**
     * @param ReportRepository $reportRepository
     * @param CommentService $commentService
     * @param ApplicationConfig $appConfig
     * @param NotificaionService $notificationService
     */
    public function __construct(ReportRepository $reportRepository, CommentService $commentService, ApplicationConfig $appConfig, NotificationService $notificationService)
    {
        $this->reportRepository = $reportRepository;
        $this->commentService = $commentService;
        $this->serializer = new Serializer();

        $this->notificationService = $notificationService;
    }

    /**
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $category
     * @return \Jimdo\Reports\Views\Report
     * @throws ReportFileRepositoryException
     */
    public function createReport(
        TraineeId $traineeId,
        string $content,
        string $date,
        string $calendarWeek,
        string $category
    ): \Jimdo\Reports\Views\Report {
        $report = $this->reportRepository->create($traineeId, $content, $date, $calendarWeek, $category);

        $event = new Events\ReportCreated([
            'userId' => $traineeId->id(),
            'reportId' => $report->id(),
            'emailSubject' => 'Bericht erstellt',
            'calendarWeek' => $calendarWeek,
            'content' => $content
        ]);
        $this->notificationService->notify($event);

        return new ReadOnlyReport($report);
    }

    /**
     * @param string $reportId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $category
     * @throws ReportFileRepositoryException
     */
    public function editReport(string $reportId, string $content, string $date, string $calendarWeek, string $category)
    {
        $report = $this->reportRepository->findById($reportId);
        $report->edit($content, $date, $calendarWeek, $category);
        $this->reportRepository->save($report);

        $event = new Events\ReportEdited([
            'userId' => $report->traineeId(),
            'reportId' => $report->id()
        ]);
        $this->notificationService->notify($event);
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

        $event = new Events\ReportDeleted([
            'userId' => $report->traineeId(),
            'reportId' => $report->id()
        ]);
        $this->notificationService->notify($event);
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

        $event = new Events\ApprovalRequested([
            'userId' => $report->traineeId(),
            'reportId' => $report->id(),
            'emailSubject' => 'Bericht eingereicht'
        ]);
        $this->notificationService->notify($event);
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

        $event = new Events\ReportApproved([
            'userId' => $report->traineeId(),
            'reportId' => $report->id(),
            'emailSubject' => 'Bericht genehmigt'
        ]);
        $this->notificationService->notify($event);
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

        $event = new Events\ReportDisapproved([
            'userId' => $report->traineeId(),
            'reportId' => $report->id(),
            'emailSubject' => 'Bericht abgelehnt',
            'calendarWeek' => $report->calendarWeek()
        ]);
        $this->notificationService->notify($event);
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
     * @param string $reportId
     * @param string $traineeId
     * @return \Jimdo\Reports\Views\Report
     * @throws ReportFileRepositoryException
     */
    public function findById(string $reportId, string $traineeId, bool $isAdmin = false)
    {
        $report = $this->reportRepository->findById($reportId);
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
        $event = new Events\CommentCreated([
            'userId' => $userId,
            'reportId' => $reportId,
            'emailSubject' => 'Kommentar erstellt',
            'commentUserId' => $userId
        ]);
        $this->notificationService->notify($event);

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
            $event = new Events\CommentEdited([
                'userId' => $comment->userId(),
                'reportId' => $comment->reportId()
            ]);
            $this->notificationService->notify($event);

            return $this->commentService->editComment($id, $newContent);
        } else {
            throw new ReportbookServiceException(
                'You are not allowed to edit this comment!',
                self::ERR_EDIT_COMMENT_DENIED
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
            $event = new Events\CommentDeleted([
                'userId' => $comment->userId(),
                'reportId' => $comment->reportId()
            ]);
            $this->notificationService->notify($event);

            $this->commentService->deleteComment($commentId);
        } else {
            throw new ReportbookServiceException(
                'You are not allowed to delete this comment!',
                self::ERR_DELETE_COMMENT_DENIED
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
     * @param array $array
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
     * @param array $array
     */
    private function sortCommentsByDate(array &$array) {
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
