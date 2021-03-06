<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View;
use Jimdo\Reports\Web\ViewHelper;
use Jimdo\Reports\Web\Validator\Validator;

use Jimdo\Reports\User\User;
use Jimdo\Reports\User\Role;
use Jimdo\Reports\User\PasswordException;
use Jimdo\Reports\User\ProfileException;
use Jimdo\Reports\User\UserService;

use Jimdo\Reports\User\PasswordConstraints\PasswordLength;
use Jimdo\Reports\User\PasswordConstraints\PasswordUpperCase;
use Jimdo\Reports\User\PasswordConstraints\PasswordLowerCase;
use Jimdo\Reports\User\PasswordConstraints\PasswordNumbers;
use Jimdo\Reports\User\PasswordConstraints\PasswordBlackList;

use Jimdo\Reports\Web\Request;
use Jimdo\Reports\Web\Response;
use Jimdo\Reports\Web\RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig;

use Jimdo\Reports\Application\ApplicationService;

use Jimdo\Reports\Notification\BrowserNotification;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;
use Jimdo\Reports\Notification\MailgunSubscriber;
use Jimdo\Reports\Notification\BrowserNotificationSubscriber;
use Jimdo\Reports\ErrorCodeStore;

class UserController extends Controller
{
    const ADMIN_DEFAULT_PASSWORD = 'Adminadmin123';
    const ADMIN_DEFAULT_USER = 'admin';

    /** @var ViewHelper */
    private $viewHelper;

    /** @var ApplicationService */
    private $appService;

    /** @var Twig_Environment */
    private $twig;

    /**
     * @param Request $request
     * @param RequestValidator $requestValidator
     * @param ApplicationConfig $appConfig
     * @param Response $response
     * @param Twig_Environment $twig
     */
    public function __construct(
        Request $request,
        RequestValidator $requestValidator,
        ApplicationConfig $appConfig,
        Response $response,
        \Twig_Environment $twig
    ) {
        parent::__construct($request, $requestValidator, $appConfig, $response, $twig);

        $this->viewHelper = new ViewHelper();
        $notificationService = new NotificationService();

        $this->appService = ApplicationService::create($appConfig, $notificationService);

        $eventTypes = [
            'usernameEdited',
            'emailEdited',
            'passwordEdited',
            'roleApproved',
            'roleDisapproved',
            'traineeRegistered',
            'trainerRegistered',
            'userAuthorized',
            'forenameEdited',
            'surnameEdited',
            'dateOfBirthEdited',
            'schoolEdited',
            'gradeEdited',
            'companyEdited',
            'jobTitleEdited',
            'trainingYearEdited',
            'startOfTrainingEdited',
            'imageEdited'
        ];

        $emailEventTypes = [
            'roleApproved',
            'roleDisapproved',
            'passwordEdited'
        ];
        $this->twig = $twig;

        $notificationService->register(new PapertrailSubscriber($eventTypes, $appConfig));
        $notificationService->register(new MailgunSubscriber($emailEventTypes, $appConfig));
        $notificationService->register(new BrowserNotificationSubscriber($eventTypes, $appConfig));

    }

    public function uploadAction()
    {
        $exceptions = [];

        $uploadOk = true;

        $allowed =  array('png', 'jpg', 'JPG', 'PNG');
        $filename = $_FILES['fileToUpload']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array($ext,$allowed) || $_FILES["fileToUpload"]['type'] === '') {
            $exceptions[] = "Folgende File-Typen sind erlaubt: JPG, JPEG, PNG.";
            $uploadOk = false;
        }

        if ($_FILES["fileToUpload"]["size"] > 2000000) {
            $exceptions[] = "Das Bild darf maximal 2MB groß sein!";
            $uploadOk = false;
        }

        if ($uploadOk) {
            $image = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
            $base64 = base64_encode($image);
            $this->appService->editImage($this->formData('userId'), $base64, $ext);

            if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
                $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
            } else {
                $this->redirect('/user/profile');
            }
        }
        $profile = $this->appService->findProfileByUserId($this->formData('userId'));
        $user = $this->appService->findUserById($this->formData('userId'));

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'layout' => $_COOKIE['LAYOUT'],
            'userId' => $this->sessionData('userId'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'hideInfos' => true,
            'isTrainee' => $this->isTrainee(),
            'profile' => $profile,
            'errorMessages' => $exceptions,
            'user' => $user,
            'notifications' => $this->notifications(),
            'appService' => $this->appService
        ];

        echo $this->twig->render('ProfileView.html', $variables);
    }

    public function indexAction()
    {
        $variables = [
            'tabTitle' => 'Berichtsheft',
            'boxShadow' => true,
            'hideFooter' => true,
            'layout' => $_COOKIE['LAYOUT']
        ];

        echo $this->twig->render('LoginView.html', $variables);
    }

    public function loginAction()
    {
        if ($this->formData('identifier') === null) {
            $_SESSION['authorized'] = false;
            $this->redirect('/user');
        }

        $identifier = $this->formData('identifier');
        $password = $this->formData('password');

        $loginWithAdminDefaultPassword = false;
        if ($identifier === self::ADMIN_DEFAULT_USER && $password === self::ADMIN_DEFAULT_PASSWORD) {
            if (!$this->appService->exists($identifier)) {
                if (!$this->appService->checkForAdmin()) {
                    $adminUser = $this->appService->registerAdmin(
                        self::ADMIN_DEFAULT_USER,
                        'admin',
                        self::ADMIN_DEFAULT_PASSWORD
                    );
                    $this->appService->createProfile($adminUser->id(), 'admin', 'admin');
                }
            }
            $loginWithAdminDefaultPassword = true;
        }
        if ($this->appService->authUser($identifier, $password)) {
            $user = $this->appService->findUserByEmail($identifier);

            if ($user === null) {
                $user = $this->appService->findUserByUsername($identifier);
            }

            if ($user->roleStatus() === Role::STATUS_APPROVED) {
                $_SESSION['role'] = $user->roleName();
                $_SESSION['authorized'] = true;
                $_SESSION['userId'] = $user->id();
                $_SESSION['username'] = $user->username();

                $profile = $this->appService->findProfileByUserId($user->id());

                if ($profile === null) {
                    $this->appService->createProfile($user->id(), ' ', ' ');
                }

                if ($loginWithAdminDefaultPassword) {
                    $this->redirect('/user/changePassword');
                } else {
                    $this->redirect('/report/list');
                }
            } else {
                $_SESSION['authorized'] = false;
                $this->redirect('/user');
            }
        } else {
            $_SESSION['authorized'] = false;
            $this->redirect('/user');
        }
    }

    public function registerAction()
    {
        $variables = [
            'tabTitle' => 'Berichtsheft',
            'hideFooter' => true,
            'role' => $this->queryParams('role'),
            'layout' => $_COOKIE['LAYOUT'],
            'boxShadow' => true
        ];

        echo $this->twig->render('RegistrationView.html', $variables);
    }

    public function createUserAction()
    {
        $forename = $this->formData('forename');
        $surname = $this->formData('surname');
        $username = $this->formData('username');
        $email = $this->formData('email');
        $password = $this->formData('password');
        $passwordConfirmation = $this->formData('passwordConfirmation');
        $role = $this->formData('role');

        $exceptions = [];

        if ($username === self::ADMIN_DEFAULT_USER) {
            $exceptions[] = $this->getErrorMessageForErrorCode(ErrorCodeStore::ERR_USERNAME_ADMIN);
        }

        if ($this->appService->exists($username)) {
            $exceptions[] = $this->getErrorMessageForErrorCode(ErrorCodeStore::ERR_USERNAME_EXISTS);
        }

        if ($this->appService->exists($email)) {
            $exceptions[] = $this->getErrorMessageForErrorCode(ErrorCodeStore::ERR_EMAIL_EXISTS);
        }

        if ($password !== $passwordConfirmation) {
            $exceptions[] = $this->getErrorMessageForErrorCode(ErrorCodeStore::PASSWORD_CONFIRMATION_WRONG_MATCHING);
        }

        if ($exceptions === []) {
            if ($role === 'TRAINER') {
                try {
                    $user = $this->appService->registerTrainer($username, $email, $password);
                    $this->appService->createProfile($user->id(), $forename, $surname);
                } catch (PasswordException $e) {
                    $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
                }
            } elseif ($role === 'TRAINEE') {
                try {
                    $user = $this->appService->registerTrainee($username, $email, $password);
                    $this->appService->createProfile($user->id(), $forename, $surname);
                } catch (PasswordException $e) {
                    $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
                }
            }
        }

        if ($exceptions !== []) {
            $variables = [
                'tabTitle' => 'Berichtsheft',
                'role' => $role,
                'hideFooter' => true,
                'errorMessages' => $exceptions,
                'boxShadow' => true
            ];

            echo $this->twig->render('RegistrationView.html', $variables);
        } else {
            header("Location: /user");
        }
    }

    public function userlistAction()
    {
        if ($this->isTrainer() || $this->isAdmin()) {
            $variables = [
                'tabTitle' => 'Berichtsheft',
                'viewHelper' => $this->viewHelper,
                'username' => $this->sessionData('username'),
                'layout' => $_COOKIE['LAYOUT'],
                'userId' => $this->sessionData('userId'),
                'role' => $this->sessionData('role'),
                'isTrainer' => $this->isTrainer(),
                'isAdmin' => $this->isAdmin(),
                'infoHeadline' => ' | Benutzeranfragen',
                'hideInfos' => false,
                'users' => $this->appService->findUsersByStatus(Role::STATUS_NOT_APPROVED),
                'profileService' => $this->appService->profileService,
                'approvedUsers' => $this->appService->findUsersByStatus(Role::STATUS_APPROVED),
                'userlistViewActive' => true,
                'notifications' => $this->notifications(),
                'appService' => $this->appService
            ];

            echo $this->twig->render('UserlistView.html', $variables);
        } else {
            $this->redirect("/user");
        }
    }

    public function deleteAction()
    {
        if ($this->isAdmin()) {
            $user = $this->appService->findUserbyEmail($this->formData('email'));
            $this->appService->deleteUser($user);
            $this->redirect('/user/userlist');
        } else {
            $this->redirect('/user');
        }
    }

    public function changeStatusAction()
    {
        if ($this->isTrainer() || $this->isAdmin()) {
            if ($this->formData('action') === 'approve') {
                $this->appService->approveUser($this->formData('email'));
            } elseif ($this->formData('action') === 'disapprove') {
                $this->appService->disapproveUser($this->formData('email'));
            }
            $this->redirect("/user/userlist");
        } else {
            $this->redirect("/user");
        }
    }

    public function profileAction()
    {
        $profile;
        $user;
        if ($this->isAdmin() && $this->queryParams('userId') !== null) {
            $profile = $this->appService->findProfileByUserId($this->queryParams('userId'));
            $user = $this->appService->findUserById($this->queryParams('userId'));
        } else {
            $profile = $this->appService->findProfileByUserId($this->sessionData('userId'));
            $user = $this->appService->findUserById($this->sessionData('userId'));
        }

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'layout' => $_COOKIE['LAYOUT'],
            'userId' => $this->sessionData('userId'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'hideInfos' => true,
            'isTrainee' => $this->isTrainee(),
            'profile' => $profile,
            'user' => $user,
            'profileViewActive' => true,
            'notifications' => $this->notifications(),
            'appService' => $this->appService
        ];

        echo $this->twig->render('ProfileView.html', $variables);
    }

    public function changeForenameAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }
        $this->appService->editForename($this->formData('userId'), $this->formData('forename'));

        if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
            $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
        } else {
            $this->redirect('/user/profile');
        }
    }

    public function changeSurnameAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }
        $this->appService->editSurname($this->formData('userId'), $this->formData('surname'));

        if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
            $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
        } else {
            $this->redirect('/user/profile');
        }
    }

    public function changeDateOfBirthAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }

        $this->addRequestValidation('dateOfBirth', 'date');

        if ($this->isRequestValid()) {
            $date = $this->formData('dateOfBirth');
            $formattedDate = date("Y-m-d", strtotime($date));

            $this->appService->editDateOfBirth($this->formData('userId'), $formattedDate);
            if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
                $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
            } else {
                $this->redirect('/user/profile');
            }
        }

        $profile = $this->appService->findProfileByUserId($this->formData('userId'));
        $user = $this->appService->findUserById($this->formData('userId'));

        $errorMessages[] = $this->getErrorMessageForErrorCode($this->requestValidator->errorCodes()['dateOfBirth']);

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'layout' => $_COOKIE['LAYOUT'],
            'userId' => $this->sessionData('userId'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'hideInfos' => true,
            'isTrainee' => $this->isTrainee(),
            'profile' => $profile,
            'user' => $user,
            'errorMessages' => $errorMessages,
            'notifications' => $this->notifications(),
            'appService' => $this->appService
        ];

        echo $this->twig->render('ProfileView.html', $variables);
    }

    public function changeUsernameAction()
    {
        $exceptions = [];
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }

        $user = $this->appService->findUserById($this->sessionData('userId'));
        $_SESSION['username'] = $this->formData('username');

        try {
            $this->appService->editUsername($this->formData('userId'), $this->formData('username'));
        } catch (ProfileException $e) {
            $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
        }

        if ($exceptions !== []) {

            $profile = $this->appService->findProfileByUserId($this->formData('userId'));
            $user = $this->appService->findUserById($this->formData('userId'));

            $variables = [
                'tabTitle' => 'Berichtsheft',
                'viewHelper' => $this->viewHelper,
                'username' => $this->sessionData('username'),
                'layout' => $_COOKIE['LAYOUT'],
                'role' => $this->sessionData('role'),
                'userId' => $this->sessionData('userId'),
                'isTrainer' => $this->isTrainer(),
                'isAdmin' => $this->isAdmin(),
                'hideInfos' => true,
                'isTrainee' => $this->isTrainee(),
                'profile' => $profile,
                'user' => $user,
                'errorMessages' => $exceptions,
                'notifications' => $this->notifications(),
                'appService' => $this->appService
            ];

            echo $this->twig->render('ProfileView.html', $variables);
        } else {
            if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
                $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
            } else {
                $this->redirect('/user/profile');
            }
        }
    }

    public function changeEmailAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }

        $exceptions = [];

        try {
            $this->appService->editEmail($this->formData('userId'), $this->formData('email'));
        } catch (ProfileException $e) {
            $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
        }

        if ($exceptions !== []) {
            $profile = $this->appService->findProfileByUserId($this->formData('userId'));
            $user = $this->appService->findUserById($this->formData('userId'));

            $variables = [
                'tabTitle' => 'Berichtsheft',
                'viewHelper' => $this->viewHelper,
                'username' => $this->sessionData('username'),
                'layout' => $_COOKIE['LAYOUT'],
                'role' => $this->sessionData('role'),
                'userId' => $this->sessionData('userId'),
                'isTrainer' => $this->isTrainer(),
                'isAdmin' => $this->isAdmin(),
                'hideInfos' => true,
                'isTrainee' => $this->isTrainee(),
                'profile' => $profile,
                'user' => $user,
                'errorMessages' => $exceptions,
                'notifications' => $this->notifications(),
                'appService' => $this->appService
            ];

            echo $this->twig->render('ProfileView.html', $variables);
        } else {
            if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
                $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
            } else {
                $this->redirect('/user/profile');
            }
        }
    }

    public function changeCompanyAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }
        $this->appService->editCompany($this->formData('userId'), $this->formData('company'));

        if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
            $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
        } else {
            $this->redirect('/user/profile');
        }
    }

    public function changeJobTitleAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }
        $this->appService->editJobTitle($this->formData('userId'), $this->formData('jobTitle'));

        if ($this->formData('userId') !== null) {
            $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
        } else {
            $this->redirect('/user/profile');
        }
    }

    public function changeSchoolAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }
        $this->appService->editSchool($this->formData('userId'), $this->formData('school'));

        if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
            $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
        } else {
            $this->redirect('/user/profile');
        }
    }

    public function changeGradeAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }
        $this->appService->editGrade($this->formData('userId'), $this->formData('grade'));

        if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
            $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
        } else {
            $this->redirect('/user/profile');
        }
    }

    public function changeStartOfTrainingAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }

        $this->addRequestValidation('startOfTraining', 'date');

        if ($this->isRequestValid()) {
            $date = $this->formData('startOfTraining');
            $formattedDate = date("Y-m-d", strtotime($date));

            $this->appService->editStartOfTraining($this->formData('userId'), $formattedDate);
            if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
                $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
            } else {
                $this->redirect('/user/profile');
            }
        }
        $errorMessages[] = $this->getErrorMessageForErrorCode($this->requestValidator->errorCodes()['startOfTraining']);

        $profile = $this->appService->findProfileByUserId($this->formData('userId'));
        $user = $this->appService->findUserById($this->formData('userId'));

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'layout' => $_COOKIE['LAYOUT'],
            'role' => $this->sessionData('role'),
            'userId' => $this->sessionData('userId'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'hideInfos' => true,
            'isTrainee' => $this->isTrainee(),
            'profile' => $profile,
            'user' => $user,
            'errorMessages' => $errorMessages,
            'notifications' => $this->notifications(),
            'appService' => $this->appService
        ];

        echo $this->twig->render('ProfileView.html', $variables);
    }

    public function changeTrainingYearAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }
        $this->addRequestValidation('trainingYear', 'integer');

        if ($this->isRequestValid()) {
            $this->appService->editTrainingYear($this->formData('userId'), $this->formData('trainingYear'));
            if ($this->isAdmin() && $this->sessionData('userId') !== $this->formData('userId')) {
                $this->redirect('/user/profile', ['userId' => $this->formData('userId')]);
            } else {
                $this->redirect('/user/profile');
            }
        }
        $errorMessages[] = $this->getErrorMessageForErrorCode($this->requestValidator->errorCodes()['trainingYear']);

        $profile = $this->appService->findProfileByUserId($this->formData('userId'));
        $user = $this->appService->findUserById($this->formData('userId'));

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'layout' => $_COOKIE['LAYOUT'],
            'role' => $this->sessionData('role'),
            'userId' => $this->sessionData('userId'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'hideInfos' => true,
            'isTrainee' => $this->isTrainee(),
            'profile' => $profile,
            'user' => $user,
            'errorMessages' => $errorMessages,
            'notifications' => $this->notifications(),
            'appService' => $this->appService
        ];

        echo $this->twig->render('ProfileView.html', $variables);
    }

    public function changePasswordAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'layout' => $_COOKIE['LAYOUT'],
            'userId' => $this->sessionData('userId'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'hideInfos' => false,
            'isTrainee' => $this->isTrainee(),
            'userId' => $this->sessionData('userId'),
            'changePasswordViewActive' => true,
            'notifications' => $this->notifications(),
            'appService' => $this->appService
        ];

        echo $this->twig->render('ChangePasswordView.html', $variables);
    }

    public function editPasswordAction()
    {
        $exceptions = [];

        if ($this->isTrainer() || $this->isTrainee() || $this->isAdmin()) {
            if ($this->formData('newPassword') === $this->formData('passwordConfirmation')) {
                try {
                    $this->appService->editPassword(
                        $this->formData('userId'),
                        $this->formData('currentPassword'),
                        $this->formData('newPassword')
                    );
                } catch (PasswordException $e) {
                    $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
                }
            } else {
                $exceptions[] =  $this->getErrorMessageForErrorCode(ErrorCodeStore::PASSWORD_CONFIRMATION_WRONG_MATCHING);
            }
        } else {
            $this->redirect("/user");
        }

        if ($exceptions !== []) {
            $variables = [
                'tabTitle' => 'Berichtsheft',
                'viewHelper' => $this->viewHelper,
                'username' => $this->sessionData('username'),
                'layout' => $_COOKIE['LAYOUT'],
                'role' => $this->sessionData('role'),
                'isTrainer' => $this->isTrainer(),
                'isAdmin' => $this->isAdmin(),
                'hideInfos' => false,
                'isTrainee' => $this->isTrainee(),
                'userId' => $this->sessionData('userId'),
                'errorMessages' => $exceptions,
                'notifications' => $this->notifications(),
                'appService' => $this->appService
            ];

            echo $this->twig->render('ChangePasswordView.html', $variables);
        } else {
            $this->redirect("/report/list");
        }
    }

    public function viewProfileAction()
    {
        $profile = $this->appService->findProfileByUserId($this->queryParams('userId'));
        $user = $this->appService->findUserById($this->queryParams('userId'));

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'layout' => $_COOKIE['LAYOUT'],
            'userId' => $this->sessionData('userId'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'hideInfos' => true,
            'isTrainee' => ($user->roleName() === Role::TRAINEE),
            'profile' => $profile,
            'user' => $user,
            'errorMessages' => $errorMessages,
            'notifications' => $this->notifications(),
            'appService' => $this->appService
        ];

        echo $this->twig->render('UserProfileView.html', $variables);
    }

    public function logoutAction()
    {
        $_SESSION['authorized'] = false;
        $_SESSION['userId'] = '';
        $_SESSION['role'] = '';

        $this->redirect("/user");
    }

    /**
     * @param int $errorCode
     */
    public function getErrorMessageForErrorCode(int $errorCode)
    {
        switch ($errorCode) {
            case ErrorCodeStore::ERR_PASSWORD_NOT_NEW:
                return 'Das neue Passwort muss anders als das derzeitige Passwort sein!' . "\n";

            case ErrorCodeStore::ERR_PASSWORD_WRONG:
                return 'Das derzeitige Passwort ist falsch!' . "\n";

            case ErrorCodeStore::ERR_USERNAME_EXISTS:
                return 'Der Benutzername existiert bereits!' . "\n";

            case ErrorCodeStore::ERR_USERNAME_EMPTY:
                return 'Der Benutzername darf nicht leer sein!' . "\n";

            case ErrorCodeStore::ERR_EMAIL_EXISTS:
                return 'Die E-Mail existiert bereits!' . "\n";

            case ErrorCodeStore::ERR_EMAIL_EMPTY:
                return 'Die E-Mail Adresse darf nicht leer sein!' . "\n";

            case ErrorCodeStore::ERR_USERNAME_ADMIN:
                return 'Der Benutzername darf nicht admin heißen!' . "\n";

            case ErrorCodeStore::ERR_VALIDATOR_DATE:
                return 'Der eingegebene Wert ist kein Datum!' . "\n";

            case ErrorCodeStore::ERR_VALIDATOR_INT:
                return 'Der eingegebene Wert ist keine Zahl!' . "\n";

            case ErrorCodeStore::PASSWORD_CONFIRMATION_WRONG_MATCHING:
                return 'Die eingegebenen Passwörter stimmen nicht überein' . "\n";

            case ErrorCodeStore::ERR_PASSWORDLENGTH:
                return 'Das Passwort muss mindestens ' .  PasswordLength::PASSWORD_LENGTH . ' Zeichen lang sein!';

            case ErrorCodeStore::ERR_PASSWORDLOWERCASE:
                return 'Das Passwort muss mindestens einen Kleinbuchstaben enthalten!';

            case ErrorCodeStore::ERR_PASSWORDUPPERCASE:
                return 'Das Passwort muss mindestens einen Großbuchstaben enthalten!';

            case ErrorCodeStore::ERR_PASSWORDNUMBERS:
                return 'Das Passwort muss mindestens 2 Zahlen enthalten!';

            case ErrorCodeStore::ERR_PASSWORDBLACKLIST:
                return 'Dieses Passwort ist nicht erlaubt!';
        }
    }

    /**
     * @return array
     */
    private function notifications()
    {
        $notifications = [];
        foreach ($this->appService->findNotificationsByUserId($this->sessionData('userId')) as $notification) {
            if ($notification->status() != BrowserNotification::STATUS_SEEN) {
                $notifications[] = $notification;
            }
        }
        return $notifications;
    }
}
