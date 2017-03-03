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
use Jimdo\Reports\Notification\Events;

class ApplicationService
{
    /** @var ReportbookService */
    public $reportbookService;

    /** @var UserService */
    public $userService;

    /** @var ProfileService */
    public $profileService;

    /** @var NotificationService */
    public $notificationService;

    /**
     * @param ReportbookService $reportbookService
     * @param UserService $userService
     * @param ProfileService $profileService
     * @param NotificationService $notificationService
     */
    public function __construct(ReportbookService $reportbookService, UserService $userService, ProfileService $profileService, NotificationService $notificationService)
    {
        $this->reportbookService = $reportbookService;
        $this->userService = $userService;
        $this->profileService = $profileService;
        $this->notificationService = $notificationService;
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

        $report = $this->reportbookService->createReport($traineeId, $content, $calendarWeek, $calendarYear, $category);

        $user = $this->findUserById($traineeId);
        $event = new Events\ReportCreated([
            'userId' => $traineeId->id(),
            'reportId' => $report->id(),
            'emailSubject' => 'Bericht erstellt',
            'calendarWeek' => $calendarWeek,
            'content' => $content,
            'username' => $user->username(),
            'email' => $user->email()
        ]);
        $this->notificationService->notify($event);

        return $report;
    }

    /**
     * @param string $reportId
     * @param string $content
     * @param string $calendarWeek
     * @param string $calendarYear
     * @param string $category
     * @throws ReportFileRepositoryException
     */
    public function editReport(string $reportId, string $content, string $calendarWeek, string $calendarYear, string $category)
    {
        $report = $this->reportbookService->reportRepository->findById($reportId);
        $this->reportbookService->editReport($reportId, $content, $calendarWeek, $calendarYear, $category);

        $event = new Events\ReportEdited([
            'userId' => $report->traineeId(),
            'reportId' => $reportId
        ]);
        $this->notificationService->notify($event);

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
        $report = $this->reportbookService->reportRepository->findById($reportId);
        $this->reportbookService->deleteReport($reportId);

        $event = new Events\ReportDeleted([
            'userId' => $report->traineeId(),
            'reportId' => $reportId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function requestApproval(string $reportId)
    {
        $this->reportbookService->requestApproval($reportId);
        $report = $this->reportbookService->reportRepository->findById($reportId);

        $user = $this->findUserById($report->taineeId());
        $event = new Events\ApprovalRequested([
            'userId' => $report->traineeId(),
            'reportId' => $reportId,
            'emailSubject' => 'Bericht eingereicht',
            'username' => $user->username(),
            'email' => $user->email()
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function approveReport(string $reportId)
    {
        $this->reportbookService->approveReport($reportId);
        $report = $this->reportbookService->reportRepository->findById($reportId);

        $user = $this->findUserById($report->traineeId());
        $event = new Events\ReportApproved([
            'userId' => $report->traineeId(),
            'reportId' => $reportId,
            'emailSubject' => 'Bericht genehmigt',
            'username' => $user->username(),
            'email' => $user->email()
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $reportId
     * @throws ReportFileRepositoryException
     */
    public function disapproveReport(string $reportId)
    {
        $this->reportbookService->disapproveReport($reportId);
        $report = $this->reportbookService->reportRepository->findById($reportId);

        $user = $this->findUserById($report->traineeId());
        $event = new Events\ReportDisapproved([
            'userId' => $report->traineeId(),
            'reportId' => $reportId,
            'emailSubject' => 'Bericht abgelehnt',
            'calendarWeek' => $report->calendarWeek(),
            'username' => $user->username(),
            'email' => $user->email()
        ]);
        $this->notificationService->notify($event);
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
        $report = $this->reportbookService->reportRepository->findById($reportId);
        $user = $this->findUserById($report->traineeId());
        $event = new Events\CommentCreated([
            'userId' => $userId,
            'reportId' => $reportId,
            'emailSubject' => 'Kommentar erstellt',
            'traineeId' => $report->traineeId(),
            'username' => $user->username(),
            'email' => $user->email()
        ]);
        $this->notificationService->notify($event);

        return $this->reportbookService->createComment($reportId, $userId, $date, $content);
    }


    /**
     * @param string $id
     * @throws ReportbookServiceException
     * @return Comment
     */
    public function editComment(string $id, string $newContent, string $userId): Comment
    {
        $comment = $this->findCommentByCommentId($id);

        $event = new Events\CommentEdited([
            'userId' => $userId,
            'reportId' => $comment->reportId()
        ]);
        $this->notificationService->notify($event);

        return $this->reportbookService->editComment($id, $newContent, $userId);
    }

    /**
     * @param string $commentId
     * @throws ReportbookServiceException
     */
    public function deleteComment(string $commentId, string $userId)
    {
        $comment = $this->findCommentByCommentId($commentId);
        $this->reportbookService->deleteComment($commentId, $userId);

        $event = new Events\CommentDeleted([
            'userId' => $userId,
            'reportId' => $comment->reportId()
        ]);
        $this->notificationService->notify($event);
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
        $user = $this->userService->registerTrainee($username, $email, $password);
        $event = new Events\TraineeRegistered([
            'userId' => $user->id()
        ]);
        $this->notificationService->notify($event);

        return $user;
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
        $user = $this->userService->registerTrainer($username, $email, $password);
        $event = new Events\TrainerRegistered([
            'userId' => $user->id()
        ]);
        $this->notificationService->notify($event);

        return $user;
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
        $user = $this->findUserById($userId);
        $event = new Events\PasswordEdited([
            'userId' => $userId,
            'emailSubject' => 'Passwortänderung',
            'username' => $user->username(),
            'email' => $user->email()
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $username
     */
    public function editUsername(string $userId, string $username)
    {
        $this->userService->editUsername($userId, $username);

        $event = new Events\UsernameEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $email
     */
    public function editEmail(string $userId, string $email)
    {
        $this->userService->editEmail($userId, $email);

        $event = new Events\EmailEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authUser(string $identifier, string $password): bool
    {
        $user = $this->findUserbyEmail($identifier);

        if ($user === null) {
            $user = $this->findUserbyUsername($identifier);
        }

        $res = $this->userService->authUser($identifier, $password);
        if ($res) {
            $event = new Events\UserAuthorized([
                'userId' => $user->id()
            ]);
            $this->notificationService->notify($event);
        }
        return $res;
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
        $user = $this->findUserByEmail($email);
        $this->userService->approveRole($email);

        $event = new Events\RoleApproved([
            'userId' => $user->id(),
            'emailSubject' => 'Zugang freigeschaltet',
            'username' => $user->username(),
            'email' => $user->email()
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $email
     */
    public function disapproveUser(string $email)
    {
        $user = $this->findUserByEmail($email);
        $this->userService->disapproveRole($email);

        $event = new Events\RoleDisapproved([
            'userId' => $user->id(),
            'emailSubject' => 'Zugang abgelehnt',
            'username' => $user->username(),
            'email' => $user->email()
        ]);
        $this->notificationService->notify($event);
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

    /**
     * @param string $userId
     * @return Profile
     */
    public function findProfileByUserId(string $userId)
    {
        return $this->profileService->findProfileByUserId($userId);
    }

    /**
     * @param Profile $deleteProfile
     */
    public function deleteProfile(Profile $deleteProfile)
    {
        $this->profileService->deleteProfile($deleteProfile);
    }

    /**
     * @param string $userId
     * @param string $forename
     */
    public function editForename(string $userId, string $forename)
    {
        $this->profileService->editForename($userId, $forename);

        $event = new Events\ForenameEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $surname
     */
    public function editSurname(string $userId, string $surname)
    {
        $this->profileService->editSurname($userId, $surname);

        $event = new Events\SurnameEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $dateOfBirth
     */
    public function editDateOfBirth(string $userId, string $dateOfBirth)
    {
        $this->profileService->editDateOfBirth($userId, $dateOfBirth);

        $event = new Events\DateOfBirthEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $school
     */
    public function editSchool(string $userId, string $school)
    {
        $this->profileService->editSchool($userId, $school);

        $event = new Events\SchoolEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $company
     */
    public function editCompany(string $userId, string $company)
    {
        $this->profileService->editCompany($userId, $company);

        $event = new Events\CompanyEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $jobTitle
     */
    public function editJobTitle(string $userId, string $jobTitle)
    {
        $this->profileService->editJobTitle($userId, $jobTitle);

        $event = new Events\JobTitleEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $trainingYear
     */
    public function editTrainingYear(string $userId, string $trainingYear)
    {
        $this->profileService->editTrainingYear($userId, $trainingYear);

        $event = new Events\TrainingYearEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $startOfTraining
     */
    public function editStartOfTraining(string $userId, string $startOfTraining)
    {
        $this->profileService->editStartOfTraining($userId, $startOfTraining);

        $event = new Events\StartOfTrainingEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $grade
     */
    public function editGrade(string $userId, string $grade)
    {
        $this->profileService->editGrade($userId, $grade);

        $event = new Events\GradeEdited([
            'userId' => $userId
        ]);
        $this->notificationService->notify($event);
    }

    /**
     * @param string $userId
     * @param string $image
     */
    public function editImage(string $userId, string $image, string $type)
    {
         $this->profileService->editImage($userId, $image, $type);

         $event = new Events\ImageEdited([
             'userId' => $userId
         ]);
         $this->notificationService->notify($event);
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
        $userService = new UserService($userRepository, $appConfig);

        $profileRepository = new ProfileMongoRepository($client, $serializer, $appConfig);
        $profileService = new ProfileService($profileRepository, $appConfig->defaultProfile, $appConfig);

        $reportRepository = new ReportMongoRepository($client, $serializer, $appConfig);
        $commentRepository = new CommentMongoRepository($client, $serializer, $appConfig);
        $commentService = new CommentService($commentRepository, $serializer, $appConfig);
        $reportbookService = new ReportbookService($reportRepository, $commentService, $appConfig);

        return new self($reportbookService, $userService, $profileService, $notificationService);
    }
}
