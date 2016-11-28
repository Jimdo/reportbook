<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Web\ViewHelper as ViewHelper;
use Jimdo\Reports\Web\Validator\Validator as Validator;

use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\User\PasswordException as PasswordException;
use Jimdo\Reports\User\ProfileException as ProfileException;
use Jimdo\Reports\User\UserService as UserService;
use Jimdo\Reports\User\UserMongoRepository as UserMongoRepository;

use Jimdo\Reports\User\PasswordConstraints\PasswordLength;
use Jimdo\Reports\User\PasswordConstraints\PasswordUpperCase;
use Jimdo\Reports\User\PasswordConstraints\PasswordLowerCase;
use Jimdo\Reports\User\PasswordConstraints\PasswordNumbers;
use Jimdo\Reports\User\PasswordConstraints\PasswordBlackList;

use Jimdo\Reports\Profile\ProfileService as ProfileService;
use Jimdo\Reports\Profile\ProfileMongoRepository as ProfileMongoRepository;

use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

use Jimdo\Reports\Serializer as Serializer;

use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;
use Jimdo\Reports\Notification\MailgunSubscriber;

class UserController extends Controller
{
    const ADMIN_DEFAULT_PASSWORD = 'Adminadmin123';
    const ADMIN_DEFAULT_USER = 'admin';
    const PASSWORD_CONFIRMATION_WRONG_MATCHING = 15;

    /** @var UserService */
    private $service;

    /** @var ProfileService */
    private $profileService;

    /** @var ViewHelper */
    private $viewHelper;

    /**
     * @param Request $request
     * @param RequestValidator $requestValidator
     * @param ApplicationConfig $appConfig
     * @param Response $response
     */
    public function __construct(
        Request $request,
        RequestValidator $requestValidator,
        ApplicationConfig $appConfig,
        Response $response
    ) {
        parent::__construct($request, $requestValidator, $appConfig, $response);

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $this->appConfig->mongoUsername
            , $this->appConfig->mongoPassword
            , $this->appConfig->mongoHost
            , $this->appConfig->mongoPort
            , $this->appConfig->mongoDatabase
        );

        $client = new \MongoDB\Client($uri);

        $notificationService = new NotificationService();
        $userRepository = new UserMongoRepository($client, new Serializer(), $appConfig);
        $this->service = new UserService($userRepository, $appConfig, $notificationService);
        $this->viewHelper = new ViewHelper();
        $profileRepository = new ProfileMongoRepository($client, new Serializer(), $appConfig);
        $this->profileService = new ProfileService($profileRepository, $appConfig->defaultProfile, $appConfig, $notificationService);

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

        $notificationService->register(new PapertrailSubscriber($eventTypes, $appConfig));
        $notificationService->register(new MailgunSubscriber($emailEventTypes, $appConfig));
    }

    public function uploadAction()
    {
        $exceptions = [];

        $uploadOk = true;

        $allowed =  array('png', 'jpg', 'JPG', 'PNG');
        $filename = $_FILES['fileToUpload']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(!in_array($ext,$allowed) ) {
            $exceptions[] = "Folgende File-Typen sind erlaubt: JPG, JPEG, PNG.";
            $uploadOk = false;
        }

        if ($_FILES["fileToUpload"]["size"] > 1000000) {
            $exceptions[] = "Das Bild darf maximal 1MB groß sein!";
            $uploadOk = false;
        }

        if ($uploadOk) {
            $image = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
            $base64 = base64_encode($image);
            $this->profileService->editImage($this->sessionData('userId'), $base64, $ext);
            $this->redirect('/user/profile');
        }

        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->hideInfos = true;

        $user = $this->service->findUserById($this->sessionData('userId'));
        $profileView = $this->view('src/Web/Controller/Views/ProfileView.php');
        $profileView->isTrainee = $this->isTrainee();
        $profile = $this->profileService->findProfileByUserId($this->sessionData('userId'));
        $profileView->errorMessages = $exceptions;
        $profileView->forename = $profile->forename();
        $profileView->surname = $profile->surname();
        $profileView->dateOfBirth = $profile->dateOfBirth();
        $profileView->company = $profile->company();
        $profileView->jobTitle = $profile->jobTitle();
        $profileView->school = $profile->school();
        $profileView->grade = $profile->grade();
        $profileView->trainingYear = $profile->trainingYear();
        $profileView->startOfTraining = $profile->startOfTraining();
        $profileView->userId = $profile->userId();
        $user = $this->service->findUserById($this->sessionData('userId'));
        $profileView->username = $user->username();
        $profileView->email = $user->email();

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');
        $footerView->backButton = true;

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($profileView->render());
        $this->response->addBody($footerView->render());
    }

    public function indexAction()
    {
        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $loginView = $this->view('src/Web/Controller/Views/LoginView.php');
        $footerView = $this->view('src/Web/Controller/Views/Footer.php');

        $footerView->backButton = false;

        $this->response->addBody($headerView->render());
        $this->response->addBody($loginView->render());
        $this->response->addBody($footerView->render());
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
            if (!$this->service->exists($identifier)) {
                if (!$this->service->checkForTrainer()) {
                    $adminUser = $this->service->registerTrainer(
                        self::ADMIN_DEFAULT_USER,
                        'admin',
                        self::ADMIN_DEFAULT_PASSWORD
                    );
                    $this->service->approveRole($adminUser->email());
                    $this->profileService->createProfile($adminUser->id(), 'admin', 'admin');
                }
            }
            $loginWithAdminDefaultPassword = true;
        }

        if ($this->service->authUser($identifier, $password)) {
            $user = $this->service->findUserByEmail($identifier);

            if ($user === null) {
                $user = $this->service->findUserByUsername($identifier);
            }

            if ($user->roleStatus() === Role::STATUS_APPROVED) {
                $_SESSION['role'] = $user->roleName();
                $_SESSION['authorized'] = true;
                $_SESSION['userId'] = $user->id();
                $_SESSION['username'] = $user->username();

                $profile = $this->profileService->findProfileByUserId($user->id());

                if ($profile === null) {
                    $this->profileService->createProfile($user->id(), ' ', ' ');
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
        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $registerView = $this->view('src/Web/Controller/Views/RegisterView.php');
        $registerView->role = $this->queryParams('role');

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');

        $this->response->addBody($headerView->render());
        $this->response->addBody($registerView->render());
        $this->response->addBody($footerView->render());
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
            $exceptions[] = $this->getErrorMessageForErrorCode(UserService::ERR_USERNAME_ADMIN);
        }
        if ($this->service->exists($username)) {
            $exceptions[] = $this->getErrorMessageForErrorCode(UserService::ERR_USERNAME_EXISTS);
        }

        if ($this->service->exists($email)) {
            $exceptions[] = $this->getErrorMessageForErrorCode(UserService::ERR_EMAIL_EXISTS);
        }

        if ($password !== $passwordConfirmation) {
            $exceptions[] = $this->getErrorMessageForErrorCode(self::PASSWORD_CONFIRMATION_WRONG_MATCHING);
        }

        if ($role === 'TRAINER') {
            try {
                $user = $this->service->registerTrainer($username, $email, $password);
                $this->profileService->createProfile($user->id(), $forename, $surname);
            } catch (PasswordException $e) {
                $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
            }
        } elseif ($role === 'TRAINEE') {
            try {
                $user = $this->service->registerTrainee($username, $email, $password);
                $this->profileService->createProfile($user->id(), $forename, $surname);
            } catch (PasswordException $e) {
                $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
            }
        }

        if ($exceptions !== []) {
            $headerView = $this->view('src/Web/Controller/Views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $registerView = $this->view('src/Web/Controller/Views/RegisterView.php');
            $registerView->role = $role;
            $registerView->errorMessages = $exceptions;

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');

            $this->response->addBody($headerView->render());
            $this->response->addBody($registerView->render());
            $this->response->addBody($footerView->render());

        } else {
            header("Location: /user");
        }
    }

    public function userlistAction()
    {
        if ($this->isTrainer()) {
            $headerView = $this->view('src/Web/Controller/Views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
            $infobarView->infoHeadline = ' | Benutzeranfragen';
            $infobarView->hideInfos = false;

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');
            $footerView->backButton = true;

            $userView = $this->view('src/Web/Controller/Views/UserlistView.php');
            $userView->users = $this->service->findUsersByStatus(Role::STATUS_NOT_APPROVED);
            $userView->viewHelper = $this->viewHelper;
            $userView->profileService = $this->profileService;

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($userView->render());
            $this->response->addBody($footerView->render());
        } else {
            $this->redirect("/user");
        }
    }

    public function changeStatusAction()
    {
        if ($this->isTrainer()) {
            if ($this->formData('action') === 'approve') {
                $this->service->approveRole($this->formData('email'));
            } elseif ($this->formData('action') === 'disapprove') {
                $this->service->disapproveRole($this->formData('email'));
            }
            $this->redirect("/user/userlist");
        } else {
            $this->redirect("/user");
        }
    }

    public function profileAction()
    {
        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->hideInfos = true;

        $profileView = $this->view('src/Web/Controller/Views/ProfileView.php');
        $profileView->isTrainee = $this->isTrainee();
        $profileView->profile = $this->profileService->findProfileByUserId($this->sessionData('userId'));
        $profile = $this->profileService->findProfileByUserId($this->sessionData('userId'));
        $profileView->forename = $profile->forename();
        $profileView->surname = $profile->surname();
        $profileView->dateOfBirth = $profile->dateOfBirth();
        $profileView->company = $profile->company();
        $profileView->jobTitle = $profile->jobTitle();
        $profileView->school = $profile->school();
        $profileView->grade = $profile->grade();
        $profileView->trainingYear = $profile->trainingYear();
        $profileView->startOfTraining = $profile->startOfTraining();
        $profileView->userId = $profile->userId();
        $user = $this->service->findUserById($this->sessionData('userId'));
        $profileView->username = $user->username();
        $profileView->email = $user->email();

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');
        $footerView->backButton = true;

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($profileView->render());
        $this->response->addBody($footerView->render());
    }

    public function changeForenameAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $this->profileService->editForename($this->sessionData('userId'), $this->formData('forename'));
        $this->redirect('/user/profile');
    }

    public function changeSurnameAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $this->profileService->editSurname($this->sessionData('userId'), $this->formData('surname'));
        $this->redirect('/user/profile');
    }

    public function changeDateOfBirthAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }

        $this->addRequestValidation('dateOfBirth', 'date');

        $user = $this->service->findUserById($this->sessionData('userId'));

        if ($this->isRequestValid()) {
            $this->profileService->editDateOfBirth($this->sessionData('userId'), $this->formData('dateOfBirth'));
            $this->redirect('/user/profile');
        }

        $errorMessages[] = $this->getErrorMessageForErrorCode($this->requestValidator->errorCodes()['dateOfBirth']);

        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->hideInfos = true;

        $profileView = $this->view('src/Web/Controller/Views/ProfileView.php');
        $profileView->isTrainee = $this->isTrainee();
        $profileView->errorMessages = $errorMessages;
        $profile = $this->profileService->findProfileByUserId($this->sessionData('userId'));
        $profileView->forename = $profile->forename();
        $profileView->surname = $profile->surname();
        $profileView->dateOfBirth = $profile->dateOfBirth();
        $profileView->company = $profile->company();
        $profileView->jobTitle = $profile->jobTitle();
        $profileView->school = $profile->school();
        $profileView->grade = $profile->grade();
        $profileView->trainingYear = $profile->trainingYear();
        $profileView->startOfTraining = $profile->startOfTraining();
        $profileView->userId = $profile->userId();
        $user = $this->service->findUserById($this->sessionData('userId'));
        $profileView->username = $user->username();
        $profileView->email = $user->email();

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($profileView->render());
        $this->response->addBody($footerView->render());
    }

    public function changeUsernameAction()
    {
        $exceptions = [];
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }

        $user = $this->service->findUserById($this->sessionData('userId'));
        $_SESSION['username'] = $this->formData('username');

        try {
            $this->service->editUsername($this->sessionData('userId'), $this->formData('username'));
        } catch (ProfileException $e) {
            $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
        }

        if ($exceptions !== []) {
            $headerView = $this->view('src/Web/Controller/Views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
            $infobarView->hideInfos = true;

            $profileView = $this->view('src/Web/Controller/Views/ProfileView.php');
            $profileView->isTrainee = $this->isTrainee();
            $profileView->errorMessages = $exceptions;
            $profile = $this->profileService->findProfileByUserId($this->sessionData('userId'));
            $profileView->forename = $profile->forename();
            $profileView->surname = $profile->surname();
            $profileView->dateOfBirth = $profile->dateOfBirth();
            $profileView->company = $profile->company();
            $profileView->jobTitle = $profile->jobTitle();
            $profileView->school = $profile->school();
            $profileView->grade = $profile->grade();
            $profileView->trainingYear = $profile->trainingYear();
            $profileView->startOfTraining = $profile->startOfTraining();
            $profileView->userId = $profile->userId();
            $user = $this->service->findUserById($this->sessionData('userId'));
            $profileView->username = $user->username();
            $profileView->email = $user->email();

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($profileView->render());
            $this->response->addBody($footerView->render());
        } else {
            $this->redirect('/user/profile');
        }
    }

    public function changeEmailAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }

        $exceptions = [];
        $user = $this->service->findUserById($this->sessionData('userId'));

        try {
            $this->service->editEmail($this->sessionData('userId'), $this->formData('email'));
        } catch (ProfileException $e) {
            $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
        }

        if ($exceptions !== []) {
            $headerView = $this->view('src/Web/Controller/Views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
            $infobarView->hideInfos = true;

            $profileView = $this->view('src/Web/Controller/Views/ProfileView.php');
            $profileView->isTrainee = $this->isTrainee();
            $profileView->errorMessages = $exceptions;
            $profile = $this->profileService->findProfileByUserId($this->sessionData('userId'));
            $profileView->forename = $profile->forename();
            $profileView->surname = $profile->surname();
            $profileView->dateOfBirth = $profile->dateOfBirth();
            $profileView->company = $profile->company();
            $profileView->jobTitle = $profile->jobTitle();
            $profileView->school = $profile->school();
            $profileView->grade = $profile->grade();
            $profileView->trainingYear = $profile->trainingYear();
            $profileView->startOfTraining = $profile->startOfTraining();
            $profileView->userId = $profile->userId();
            $user = $this->service->findUserById($this->sessionData('userId'));
            $profileView->username = $user->username();
            $profileView->email = $user->email();

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($profileView->render());
            $this->response->addBody($footerView->render());
        } else {
            $this->redirect('/user/profile');
        }
    }

    public function changeCompanyAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $user = $this->service->findUserById($this->sessionData('userId'));
        $this->profileService->editCompany($this->sessionData('userId'), $this->formData('company'));
        $this->redirect('/user/profile');
    }

    public function changeJobTitleAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $user = $this->service->findUserById($this->sessionData('userId'));
        $this->profileService->editJobTitle($this->sessionData('userId'), $this->formData('jobTitle'));
        $this->redirect('/user/profile');
    }

    public function changeSchoolAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $user = $this->service->findUserById($this->sessionData('userId'));
        $this->profileService->editSchool($this->sessionData('userId'), $this->formData('school'));
        $this->redirect('/user/profile');
    }

    public function changeGradeAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $user = $this->service->findUserById($this->sessionData('userId'));
        $this->profileService->editGrade($this->sessionData('userId'), $this->formData('grade'));
        $this->redirect('/user/profile');
    }

    public function changeStartOfTrainingAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }

        $this->addRequestValidation('startOfTraining', 'date');
        $user = $this->service->findUserById($this->sessionData('userId'));

        if ($this->isRequestValid()) {
            $this->profileService->editStartOfTraining($this->sessionData('userId'), $this->formData('startOfTraining'));
            $this->redirect('/user/profile');
        }

        $errorMessages[] = $this->getErrorMessageForErrorCode($this->requestValidator->errorCodes()['startOfTraining']);

        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->hideInfos = true;

        $profileView = $this->view('src/Web/Controller/Views/ProfileView.php');
        $profileView->isTrainee = $this->isTrainee();
        $profileView->errorMessages = $errorMessages;
        $profile = $this->profileService->findProfileByUserId($this->sessionData('userId'));
        $profileView->forename = $profile->forename();
        $profileView->surname = $profile->surname();
        $profileView->dateOfBirth = $profile->dateOfBirth();
        $profileView->company = $profile->company();
        $profileView->jobTitle = $profile->jobTitle();
        $profileView->school = $profile->school();
        $profileView->grade = $profile->grade();
        $profileView->trainingYear = $profile->trainingYear();
        $profileView->startOfTraining = $profile->startOfTraining();
        $profileView->userId = $profile->userId();
        $user = $this->service->findUserById($this->sessionData('userId'));
        $profileView->username = $user->username();
        $profileView->email = $user->email();

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($profileView->render());
        $this->response->addBody($footerView->render());
    }

    public function changeTrainingYearAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $this->addRequestValidation('trainingYear', 'integer');
        $user = $this->service->findUserById($this->sessionData('userId'));

        if ($this->isRequestValid()) {
            $this->profileService->editTrainingYear($this->sessionData('userId'), $this->formData('trainingYear'));
            $this->redirect('/user/profile');
        }
        $errorCodes = $this->requestValidator->errorCodes();
        $errorMessages[] = $this->getErrorMessageForErrorCode($errorCodes['trainingYear']);

        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->hideInfos = true;

        $profileView = $this->view('src/Web/Controller/Views/ProfileView.php');
        $profileView->isTrainee = $this->isTrainee();
        $profileView->errorMessages = $errorMessages;
        $profile = $this->profileService->findProfileByUserId($this->sessionData('userId'));
        $profileView->forename = $profile->forename();
        $profileView->surname = $profile->surname();
        $profileView->dateOfBirth = $profile->dateOfBirth();
        $profileView->company = $profile->company();
        $profileView->jobTitle = $profile->jobTitle();
        $profileView->school = $profile->school();
        $profileView->grade = $profile->grade();
        $profileView->trainingYear = $profile->trainingYear();
        $profileView->startOfTraining = $profile->startOfTraining();
        $profileView->userId = $profile->userId();
        $user = $this->service->findUserById($this->sessionData('userId'));
        $profileView->username = $user->username();
        $profileView->email = $user->email();

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($profileView->render());
        $this->response->addBody($footerView->render());
    }

    public function changePasswordAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }

        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->hideInfos = true;

        $changePasswordView = $this->view('src/Web/Controller/Views/ChangePasswordView.php');

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($changePasswordView->render());
        $this->response->addBody($footerView->render());
    }

    public function editPasswordAction()
    {
        $exceptions = [];

        if ($this->isTrainer() || $this->isTrainee()) {
            if ($this->formData('newPassword') === $this->formData('passwordConfirmation')) {
                try {
                    $this->service->editPassword(
                        $this->sessionData('userId'),
                        $this->formData('currentPassword'),
                        $this->formData('newPassword')
                    );
                } catch (PasswordException $e) {
                    $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
                }
            } else {
                $exceptions[] =  $this->getErrorMessageForErrorCode(self::PASSWORD_CONFIRMATION_WRONG_MATCHING);
            }
        } else {
            $this->redirect("/user");
        }

        if ($exceptions !== []) {
            $headerView = $this->view('src/Web/Controller/Views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
            $infobarView->hideInfos = true;

            $changePasswordView = $this->view('src/Web/Controller/Views/ChangePasswordView.php');
            $changePasswordView->errorMessages = $exceptions;

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($changePasswordView->render());
            $this->response->addBody($footerView->render());
        } else {
            $this->redirect("/report/list");
        }
    }

    public function viewProfileAction()
    {
        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->hideInfos = true;

        $viewProfileView = $this->view('src/Web/Controller/Views/UserProfileView.php');
        $profile = $this->profileService->findProfileByUserId($this->queryParams('userId'));
        $viewProfileView->forename = $profile->forename();
        $viewProfileView->surname = $profile->surname();
        $viewProfileView->dateOfBirth = $profile->dateOfBirth();
        $viewProfileView->company = $profile->company();
        $viewProfileView->jobTitle = $profile->jobTitle();
        $viewProfileView->school = $profile->school();
        $viewProfileView->grade = $profile->grade();
        $viewProfileView->trainingYear = $profile->trainingYear();
        $viewProfileView->startOfTraining = $profile->startOfTraining();
        $viewProfileView->userId = $profile->userId();
        $user = $this->service->findUserById($this->queryParams('userId'));
        $viewProfileView->username = $user->username();
        $viewProfileView->email = $user->email();
        $viewProfileView->isTrainee = ($user->roleName() === 'TRAINEE');

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');
        $footerView->backButton = true;

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($viewProfileView->render());
        $this->response->addBody($footerView->render());
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
            case User::ERR_PASSWORD_NOT_NEW:
                return 'Das neue Passwort muss anders als das derzeitige Passwort sein!' . "\n";

            case User::ERR_PASSWORD_WRONG:
                return 'Das derzeitige Passwort ist falsch!' . "\n";

            case UserService::ERR_USERNAME_EXISTS:
                return 'Der Benutzername existiert bereits!' . "\n";

            case UserService::ERR_USERNAME_EMPTY:
                return 'Der Benutzername darf nicht leer sein!' . "\n";

            case UserService::ERR_EMAIL_EXISTS:
                return 'Die E-Mail existiert bereits!' . "\n";

            case UserService::ERR_EMAIL_EMPTY:
                return 'Die E-Mail Adresse darf nicht leer sein!' . "\n";

            case UserService::ERR_USERNAME_ADMIN:
                return 'Der Benutzername darf nicht admin heißen!' . "\n";

            case Validator::ERR_VALIDATOR_DATE:
                return 'Der eingegebene Wert ist kein Datum!' . "\n";

            case Validator::ERR_VALIDATOR_INT:
                return 'Der eingegebene Wert ist keine Zahl!' . "\n";

            case self::PASSWORD_CONFIRMATION_WRONG_MATCHING:
                return 'Die eingegebenen Passwörter stimmen nicht überein' . "\n";

            case PasswordLength::ERR_CODE:
                return 'Das Passwort muss mindestens ' .  PasswordLength::PASSWORD_LENGTH . ' Zeichen lang sein!';

            case PasswordLowerCase::ERR_CODE:
                return 'Das Passwort muss mindestens einen Kleinbuchstaben enthalten!';

            case PasswordUpperCase::ERR_CODE:
                return 'Das Passwort muss mindestens einen Großbuchstaben enthalten!';

            case PasswordNumbers::ERR_CODE:
                return 'Das Passwort muss mindestens 2 Zahlen enthalten!';

            case PasswordBlackList::ERR_CODE:
                return 'Dieses Passwort ist nicht erlaubt!';
        }
    }
}
