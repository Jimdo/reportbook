<?php

namespace Jimdo\Reports\Application;

use Jimdo\Reports\Reportbook\CommentService;
use Jimdo\Reports\Reportbook\Comment;
use Jimdo\Reports\Reportbook\ReportbookService;
use Jimdo\Reports\Reportbook\Report;
use Jimdo\Reports\Reportbook\TraineeId;

use Jimdo\Reports\User\UserService;
use Jimdo\Reports\User\Role;

use Jimdo\Reports\Profile\ProfileService;

use Jimdo\Reports\Printer\PrintService;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Serializer;
use Jimdo\Reports\RepositoryFactory;
use Jimdo\Reports\SerializerFactory;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\Subscriber;
use Jimdo\Reports\Notification\BrowserNotificationService;
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

    /** @var BrowserNotificationService */
    public $browserNotificationService;

    /**
     * @param ReportbookService $reportbookService
     * @param UserService $userService
     * @param ProfileService $profileService
     * @param NotificationService $notificationService
     * @param PrintService $printService
     */
    public function __construct(ReportbookService $reportbookService, UserService $userService, ProfileService $profileService, NotificationService $notificationService, PrintService $printService, BrowserNotificationService $browserNotificationService)
    {
        $this->reportbookService = $reportbookService;
        $this->userService = $userService;
        $this->profileService = $profileService;
        $this->notificationService = $notificationService;
        $this->printService = $printService;
        $this->browserNotificationService = $browserNotificationService;
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

        $user = $this->findUserById($traineeId->id());
        $event = new Events\ReportCreated([
            'userId' => $traineeId->id(),
            'reportId' => $report->id(),
            'emailSubject' => 'Bericht erstellt',
            'calendarWeek' => $calendarWeek,
            'calendarYear' => $calendarYear,
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
     * @param string $status
     * @throws ReportFileRepositoryException
     */
    public function editReport(string $reportId, string $content, string $calendarWeek, string $calendarYear, string $category, string $status = null)
    {
        $report = $this->reportbookService->reportRepository->findById($reportId);

        if ($report->status() !== $status && $status !== null) {
            switch ($status) {
                case Report::STATUS_APPROVAL_REQUESTED:
                    $this->requestApproval($report->id());
                    break;
                case Report::STATUS_APPROVED:
                    $this->approveReport($report->id());
                    break;
                case Report::STATUS_DISAPPROVED:
                    $this->disapproveReport($report->id());
                    break;
                default:
                    break;
            }
        } else {
            $this->reportbookService->editReport($reportId, $content, $calendarWeek, $calendarYear, $category);
            $event = new Events\ReportEdited([
                'userId' => $report->traineeId(),
                'reportId' => $reportId
            ]);
            $this->notificationService->notify($event);
        }
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

        $user = $this->findUserById($report->traineeId());
        $event = new Events\ApprovalRequested([
            'userId' => $report->traineeId(),
            'reportId' => $reportId,
            'emailSubject' => 'Bericht eingereicht',
            'username' => $user->username(),
            'email' => $user->email(),
            'trainers' => $this->findAllTrainers()
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
            'calendarYear' => $report->calendarYear(),
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
     * @param string $currentReportId
     * @param string $traineeId
     * @return \Jimdo\Reports\Report
     */
    public function findNextReport(string $currentReportId, string $traineeId)
    {
        return $this->reportbookService->findNextReport($currentReportId, $traineeId);
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
            'email' => $user->email(),
            'calendarWeek' => $report->calendarWeek(),
            'calendarYear' => $report->calendarYear()
        ]);
        $this->notificationService->notify($event);

        return $this->reportbookService->createComment($reportId, $userId, $date, $content);
    }

    /**
     * @param string $id
     * @param string $newContent
     * @param string $userId
     * @param string $reportId
     * @return Comment
     */
    public function editCommentForReport(string $id, string $userId, string $reportId, string $newContent): Comment
    {
        $comment = $this->findCommentByCommentId($id);

        if ($comment->reportId() === $reportId) {
            return $this->editComment($id, $newContent, $userId);
        }
    }

    /**
     * @param string $id
     * @param string $newContent
     * @param string $userId
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
     * @param string $role
     * @param string $username
     * @param string $email
     * @param string $forename
     * @param string $surname
     * @param string $password
     */
    public function registerUser(string $role, string $username, string $email, string $forename, string $surname, string $password)
    {
        if ($role === Role::TRAINER) {
            $user = $this->registerTrainer($username, $email, $password);
            $this->createProfile($user->id(), $forename, $surname);
        }

        if ($role === Role::TRAINEE) {
            $user = $this->registerTrainee($username, $email, $password);
            $this->createProfile($user->id(), $forename, $surname);
        }
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
     * @param string $userId
     * @param string $username
     * @param string $email
     */
    public function editUser(string $userId, string $username, string $email)
    {
        $user = $this->findUserById($userId);

        if ($user->username() !== $username) {
            $this->editUsername($userId, $username);
        }

        if ($user->email() !== $email) {
            $this->editEmail($userId, $email);
        }
        return $this->findUserById($userId);
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
     * @return array
     */
    public function findAllTrainers(): array
    {
        return $this->userService->findAllTrainers();
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
     * @param string $surname
     * @param string $dateOfBirth
     * @param string $school
     * @param string $company
     * @param string $jobTitle
     * @param string $trainingYear
     * @param string $startOfTraining
     * @param string $grade
     */
    public function editProfile(
        string $userId,
        string $forename,
        string $surname,
        string $dateOfBirth,
        string $school,
        string $company,
        string $jobTitle,
        string $trainingYear,
        string $startOfTraining,
        string $grade
    ) {
            $this->editForename ($userId, $forename);
            $this->editSurname($userId, $surname);
            $this->editDateOfBirth($userId, $dateOfBirth);
            $this->editSchool($userId, $school);
            $this->editCompany($userId, $company);
            $this->editJobTitle($userId, $jobTitle);
            $this->editTrainingYear($userId, $trainingYear);
            $this->editStartOfTraining($userId, $startOfTraining);
            $this->editGrade($userId, $grade);

            return $this->findProfileByUserId($userId);
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

    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     * @return BrowserNotification
     */
    public function createNotification(string $title, string $description, string $userId, string $reportId) : BrowserNotification
    {
        return $this->browserNotificationService->create($title, $description, $userId, $reportId);
    }

    /**
     * @param BrowserNotification $notification
     */
    public function deleteNotification(BrowserNotification $notification)
    {
        $this->browserNotificationService->delete($notification);
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findNotificationsByUserId(string $userId) : array
    {
        return $this->browserNotificationService->findByUserId($userId);
    }

    /**
     * @param string $status
     * @return array
     */
    public function findNotificationsByStatus(string $status) : array
    {
        return $this->browserNotificationService->findByStatus($status);
    }

    /**
     * @param string $id
     * @return BrowserNotification | null
     */
    public function findNotificationById(string $id)
    {
        return $this->browserNotificationService->findById($id);
    }

    /**
     * @param string $id
     */
    public function notificationSeen(string $id)
    {
        $notification = $this->browserNotificationService->findById($id);
        $notification->seen();
        $this->browserNotificationService->save($notification);
    }

    /**
     * @param string $userId
     * @param string $trainerTitle
     * @param string $trainerForename
     * @param string $trainerSurname
     * @param string $companyStreet
     * @param string $companyCity
     * @param bool $printWholeReportbook
     */
    public function printCover(string $userId, string $trainerTitle, string $trainerForename, string $trainerSurname, string $companyStreet, string $companyCity, bool $printWholeReportbook = false)
    {
        $this->printService->printCover($userId, $trainerTitle, $trainerForename, $trainerSurname, $companyStreet, $companyCity);
    }

    /**
    * @param string $userId
    * @param string $startMonth
    * @param string $startYear
    * @param string $endMonth
    * @param string $endYear
    * @param bool $printWholeReportbook
    */
    public function printReports(string $userId, string $startMonth, string $startYear, string $endMonth, string $endYear, bool $printWholeReportbook = false)
    {
        $this->printService->printReports($userId, $startMonth, $startYear, $endMonth, $endYear);
    }

    /**
     * @param string $userId
     * @param string $trainerTitle
     * @param string $trainerForename
     * @param string $trainerSurname
     * @param string $companyStreet
     * @param string $companyCity
     */
    public function printReportbook(string $userId, string $trainerTitle, string $trainerForename, string $trainerSurname, string $companyStreet, string $companyCity)
    {
        $this->printService->printReportbook($userId, $trainerTitle, $trainerForename, $trainerSurname, $companyStreet, $companyCity);
    }

    /**
     * @param Subscriber $subscriber
     */
    public function registerSubscriber(Subscriber $subscriber)
    {
        $this->notificationService->register($subscriber);
    }

    public static function create(ApplicationConfig $appConfig)
    {
        $repositoryFactory = new RepositoryFactory($appConfig);
        $serializerFactory = new SerializerFactory($appConfig);

        $serializer = $serializerFactory->createSerializer();

        $userRepository = $repositoryFactory->createUserRepository();
        $profileRepository = $repositoryFactory->createProfileRepository();
        $commentRepository = $repositoryFactory->createCommentRepository();
        $reportRepository = $repositoryFactory->createReportRepository();
        $browserNotificationRepository = $repositoryFactory->createBrowserNotificationRepository();

        $userService = new UserService($userRepository);
        $profileService = new ProfileService($profileRepository, $appConfig->defaultProfile);
        $commentService = new CommentService($commentRepository);
        $reportbookService = new ReportbookService($reportRepository, $commentService, $serializer);
        $browserNotificationService = new BrowserNotificationService($browserNotificationRepository);
        $notificationService = new NotificationService();

        $printService = new PrintService($profileService, $reportbookService, $appConfig);

        return new self($reportbookService, $userService, $profileService, $notificationService, $printService, $browserNotificationService);
    }
}
