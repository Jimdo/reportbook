<?php

namespace Jimdo\Reports\Application;

use Jimdo\Reports\Reportbook\CommentMongoRepository;
use Jimdo\Reports\Reportbook\CommentService;
use Jimdo\Reports\Reportbook\ReportbookService;
use Jimdo\Reports\Reportbook\ReportMongoRepository;
use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\Reportbook\TraineeId as TraineeId;

use Jimdo\Reports\User\UserMongoRepository;
use Jimdo\Reports\User\UserService;

use Jimdo\Reports\Profile\ProfileMongoRepository;
use Jimdo\Reports\Profile\ProfileService;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Serializer;
use Jimdo\Reports\Notification\NotificationService;

class ApplicationService
{
    /** @var ReportbookService */
    public $reportbookService;

    /** @var UserService */
    public $userService;

    /** @var ProfileService */
    public $profileService;

    /**
     * @param ReportbookService $reportbookService
     * @param UserService $userService
     * @param ProfileService $profileService
     */
    public function __construct(ReportbookService $reportbookService, UserService $userService, ProfileService $profileService)
    {
        $this->reportbookService = $reportbookService;
        $this->userService = $userService;
        $this->profileService = $profileService;
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
        return $this->reportbookService->createReport($traineeId, $content, $calendarWeek, $calendarYear, $category);
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
        $this->reportbookService->editReport($reportId, $content, $calendarWeek, $calendarYear, $category);
    }

    /**
     * @return \Jimdo\Reports\Views\Report[]
     * @throws ReportFileRepositoryException
     */
    public function findAllReports(): array
    {
        return $this->reportbookService->findAll();
    }

    /**
     * @param string $traineeId
     * @return \Jimdo\Reports\Views\Report[]
     * @throws ReportFileRepositoryException
     */
    public function findReportsByTraineeId(string $traineeId): array
    {
        return $this->reportbookService->findByTraineeId($traineeId);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function deleteReport(string $reportId)
    {
        $this->reportbookService->deleteReport($reportId);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function requestApproval(string $reportId)
    {
        $this->reportbookService->requestApproval($reportId);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function approveReport(string $reportId)
    {
        $this->reportbookService->approveReport($reportId);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function disapproveReport(string $reportId)
    {
        $this->reportbookService->disapproveReport($reportId);
    }

    /*
     * @param $user
     */
    public function deleteUser(\Jimdo\Reports\User\User $user)
    {
        $userId = $user->id();

        $comments = $this->reportbookService->findCommentsByUserId($userId);
        foreach ($comments as $comment) {
            $this->reportbookService->deleteComment($comment->id(), $userId);
        }

        $reports = $this->reportbookService->findByTraineeId($userId);
        if ($reports !== []) {
            foreach ($reports as $report) {
                $this->reportbookService->deleteReport($report->id());
            }
        }

        $profile = $this->profileService->findProfileByUserId($userId);
        if ($profile !== null) {
            $this->profileService->deleteProfile($profile);
        }

        $user = $this->userService->findUserById($userId);
        $this->userService->deleteUser($user);
    }

    public static function create(ApplicationConfig $appConfig, NotificationService $notificationService)
    {
        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $appConfig->mongoUsername
            , $appConfig->mongoPassword
            , $appConfig->mongoHost
            , $appConfig->mongoPort
            , $appConfig->mongoDatabase
        );

        $client = new \MongoDB\Client($uri);
        $serializer = new Serializer();

        $userRepository = new UserMongoRepository($client, $serializer, $appConfig);
        $userService = new UserService($userRepository, $appConfig, $notificationService);

        $profileRepository = new ProfileMongoRepository($client, $serializer, $appConfig);
        $profileService = new ProfileService($profileRepository, $appConfig->defaultProfile, $appConfig, $notificationService);

        $reportRepository = new ReportMongoRepository($client, $serializer, $appConfig);
        $commentRepository = new CommentMongoRepository($client, $serializer, $appConfig);
        $commentService = new CommentService($commentRepository, $serializer, $appConfig);
        $reportbookService = new ReportbookService($reportRepository, $commentService, $appConfig, $notificationService);

        return new self($reportbookService, $userService, $profileService);
    }
}
