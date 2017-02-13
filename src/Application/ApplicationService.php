<?php

namespace Jimdo\Reports\Application;

use Jimdo\Reports\Reportbook\CommentMongoRepository;
use Jimdo\Reports\Reportbook\CommentService;
use Jimdo\Reports\Reportbook\Comment;
use Jimdo\Reports\Reportbook\ReportbookService;
use Jimdo\Reports\Reportbook\ReportMongoRepository;
use Jimdo\Reports\Reportbook\Report;
use Jimdo\Reports\Reportbook\TraineeId;

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

    /**
     * @param string $status
     * @return array
     * @throws ReportFileRepositoryException
     */
    public function findReportsByStatus(string $status): array
    {
        return $this->reportbookService->findByStatus($status);
    }

    /**
     * @param string $text
     * @return array
     */
    public function findReportsByString(string $text, string $userId, string $role): array
    {
        return $this->reportbookService->findReportsByString($text, $userId, $role);
    }

    /**
     * @param string $reportId
     * @param string $traineeId
     * @return \Jimdo\Reports\Views\Report
     * @throws ReportFileRepositoryException
     */
    public function findReportById(string $reportId, string $traineeId, bool $isAdmin = false)
    {
        return $this->reportbookService->findById($reportId, $traineeId, $isAdmin);
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
        return $this->reportbookService->createComment($reportId, $userId, $date, $content);
    }


    /**
     * @param string $id
     * @throws ReportbookServiceException
     * @return Comment
     */
    public function editComment(string $id, string $newContent, string $userId): Comment
    {
        return $this->reportbookService->editComment($id, $newContent, $userId);
    }

    /**
     * @param string $commentId
     * @throws ReportbookServiceException
     */
    public function deleteComment(string $commentId, string $userId)
    {
        $this->reportbookService->deleteComment($commentId, $userId);
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function findCommentsByReportId(string $reportId): array
    {
        return $this->reportbookService->findCommentsByReportId($reportId);
    }

    /**
     * @param string $commentId
     * @return Comment
     */
    public function findCommentByCommentId(string $commentId): Comment
    {
        return $this->reportbookService->findCommentById($commentId);
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findCommentsByUserId(string $userId): array
    {
        return $this->reportbookService->findCommentsByUserId($userId);
    }

    /**
     * @param string $key
     * @param array $array
     */
    public function sortArrayDescending(string $key, array &$array)
    {
        $this->reportbookService->sortArrayDescending($key, $array);
    }

    /**
     * @param array $status
     * @param array $array
     */
    public function sortReportsByStatus(array $status, array &$reports)
    {
        $this->reportbookService->sortReportsByStatus($status, $reports);
    }

    /**
     * @param array $reportArray
     */
    public function sortReportsByAmountOfComments(array &$reportArray)
    {
        $this->reportbookService->sortReportsByAmountOfComments($reportArray);
    }

    /**
     * @param array $reports
     * @return array
     */
    public function sortReportsByCalendarWeekAndYear(array $reports): array
    {
        return $this->reportbookService->sortReportsByCalendarWeekAndYear($reports);
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    public function registerTrainee(
        string $username,
        string $email,
        string $password
    ) {
        return $this->userService->registerTrainee($username, $email, $password);
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    public function registerTrainer(
        string $username,
        string $email,
        string $password
    ) {
        return $this->userService->registerTrainer($username, $email, $password);
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    public function registerAdmin(
        string $username,
        string $email,
        string $password
    ) {
        return $this->userService->registerAdmin($username, $email, $password);
    }

    /**
     * @param string $userId
     * @param string $oldPassword
     * @param string $newPassword
     */
    public function editPassword(string $userId, string $oldPassword, string $newPassword)
    {
        $this->userService->editPassword($userId, $oldPassword, $newPassword);
    }

    /**
     * @param string $userId
     * @param string $username
     */
    public function editUsername(string $userId, string $username)
    {
        $this->userService->editUsername($userId, $username);
    }

    /**
     * @param string $userId
     * @param string $email
     */
    public function editEmail(string $userId, string $email)
    {
        $this->userService->editEmail($userId, $email);
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authUser(string $identifier, string $password): bool
    {
        return $this->userService->authUser($identifier, $password);
    }

    /**
     * @return bool
     */
    public function checkForAdmin(): bool
    {
        return $this->userService->checkForAdmin();
    }

    /**
     * @param string $status
     * @return array
     */
    public function findUsersByStatus(string $status): array
    {
        return $this->userService->findUsersByStatus($status);
    }

    /**
     * @param string $id
     * @return User
     */
    public function findUserById(string $id)
    {
        return $this->userService->findUserById($id);
    }

    /**
     * @param string $username
     * @return User
     */
    public function findUserByUsername(string $username)
    {
        return $this->userService->findUserByUsername($username);
    }

    /**
     * @param string $status
     * @return User
     */
    public function findUserByEmail(string $email)
    {
        return $this->userService->findUserByEmail($email);
    }

    /**
     * @return array
     */
    public function findAllTrainees(): array
    {
        return $this->userService->findAllTrainees();
    }

    /**
     * @param string $email
     */
    public function approveUser(string $email)
    {
        $this->userService->approveRole($email);
    }

    /**
     * @param string $email
     */
    public function disapproveUser(string $email)
    {
        $this->userService->disapproveRole($email);
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool
    {
        return $this->userService->exists($identifier);
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

    /**
     * @param string $userId
     * @param string $forename
     * @param string $surname
     * @return Profile
     */
    public function createProfile(string $userId, string $forename, string $surname)
    {
        return $this->profileService->createProfile($userId, $forename, $surname);
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
