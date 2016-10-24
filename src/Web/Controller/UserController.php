<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Web\ViewHelper as ViewHelper;
use Jimdo\Reports\Web\Validator\Validator as Validator;
use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\User\UserService as UserService;
use Jimdo\Reports\User\UserMongoRepository as UserMongoRepository;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\User\PasswordException as PasswordException;
use Jimdo\Reports\Serializer as Serializer;
use Jimdo\Reports\User\ProfileException as ProfileException;

class UserController extends Controller
{
    const ADMIN_DEFAULT_PASSWORD = 'adminadmin';
    const ADMIN_DEFAULT_USER = 'admin';

    /** @var UserService */
    private $service;

    /** @var ViewHelper */
    private $viewHelper;

    /**
     * @param Request $request
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

        $userRepository = new UserMongoRepository($client, new Serializer(), $appConfig);
        $this->service = new UserService($userRepository);
        $this->viewHelper = new ViewHelper();
    }

    public function uploadAction()
    {
        $uploadOk = true;

        $allowed =  array('gif','png', 'jpg', 'JPG', 'PNG');
        $filename = $_FILES['fileToUpload']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(!in_array($ext,$allowed) ) {
            echo "Folgende File-Typen sind erlaubt: JPG, JPEG, PNG, GIF. \n";
            $uploadOk = false;
        }

        if ($_FILES["fileToUpload"]["size"] > 1000000) {
            echo "Das Bild darf maximal 1MB groß sein! \n";
            $uploadOk = false;
        }

        if ($uploadOk) {
            $image = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
            $base64 = base64_encode($image);
            $this->service->editImage($this->sessionData('userId'), $base64);
            $this->redirect('/user/profile');
        }
    }

    public function indexAction()
    {
        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $loginView = $this->view('app/views/LoginView.php');
        $footerView = $this->view('app/views/Footer.php');

        $footerView->backButton = 'nope';

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
                $adminUser = $this->service->registerTrainer(
                    'admin',
                    'admin',
                    self::ADMIN_DEFAULT_USER,
                    'admin',
                    self::ADMIN_DEFAULT_PASSWORD
                );
                $this->service->approveRole($adminUser->email());
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
        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $registerView = $this->view('app/views/RegisterView.php');
        $registerView->role = $this->queryParams('role');

        $footerView = $this->view('app/views/Footer.php');

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

        if ($password !== $passwordConfirmation) {
            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $registerView = $this->view('app/views/RegisterView.php');
            $registerView->role = $role;
            $registerView->errorMessages = ['Die eingegebenen Passwörter stimmen nicht überein'];
            $footerView = $this->view('app/views/Footer.php');

            $this->response->addBody($headerView->render());
            $this->response->addBody($registerView->render());
            $this->response->addBody($footerView->render());
        } else {
            if ($role === 'TRAINER') {
                $this->service->registerTrainer($forename, $surname, $username, $email, $password);
                header("Location: /user");
            } elseif ($role === 'TRAINEE') {
                $this->service->registerTrainee($forename, $surname, $username, $email, $password);
                header("Location: /user");
            } else {
                header("Location: /user");
            }
        }
    }

    public function userlistAction()
    {
        if ($this->isTrainer()) {
            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->infoHeadline = ' | Benutzeranfragen';
            $infobarView->hideInfos = false;

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            $userView = $this->view('app/views/UserlistView.php');
            $userView->users = $this->service->findUsersByStatus(Role::STATUS_NOT_APPROVED);
            $userView->viewHelper = $this->viewHelper;

            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

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
        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->hideInfos = true;

        $profileView = $this->view('app/views/ProfileView.php');
        $profileView->user = $this->service->findUserById($this->sessionData('userId'));

        $footerView = $this->view('app/views/Footer.php');
        $footerView->backButton = 'show';

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
        $this->service->editForename($this->sessionData('userId'), $this->formData('forename'));
        $this->redirect('/user/profile');
    }

    public function changeSurnameAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $this->service->editSurname($this->sessionData('userId'), $this->formData('surname'));
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
            $this->service->editDateOfBirth($this->sessionData('userId'), $this->formData('dateOfBirth'));
            $this->redirect('/user/profile');
        }

        $errorMessages[] = $this->getErrorMessageForErrorCode($this->requestValidator->errorCodes()['dateOfBirth']);

        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->hideInfos = true;

        $profileView = $this->view('app/views/ProfileView.php');
        $profileView->user = $this->service->findUserById($this->sessionData('userId'));
        $profileView->errorMessages = $errorMessages;

        $footerView = $this->view('app/views/Footer.php');

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
            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->hideInfos = true;

            $profileView = $this->view('app/views/ProfileView.php');
            $profileView->user = $this->service->findUserById($this->sessionData('userId'));
            $profileView->errorMessages = $exceptions;

            $footerView = $this->view('app/views/Footer.php');

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
            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->hideInfos = true;

            $profileView = $this->view('app/views/ProfileView.php');
            $profileView->user = $this->service->findUserById($this->sessionData('userId'));
            $profileView->errorMessages = $exceptions;

            $footerView = $this->view('app/views/Footer.php');

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
        $this->service->editCompany($this->sessionData('userId'), $this->formData('company'));
        $this->redirect('/user/profile');
    }

    public function changeJobTitleAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $user = $this->service->findUserById($this->sessionData('userId'));
        $this->service->editJobTitle($this->sessionData('userId'), $this->formData('jobTitle'));
        $this->redirect('/user/profile');
    }

    public function changeSchoolAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $user = $this->service->findUserById($this->sessionData('userId'));
        $this->service->editSchool($this->sessionData('userId'), $this->formData('school'));
        $this->redirect('/user/profile');
    }

    public function changeGradeAction()
    {
        if (!$this->isTrainer() && !$this->isTrainee()) {
            $this->redirect("/user");
        }
        $user = $this->service->findUserById($this->sessionData('userId'));
        $this->service->editGrade($this->sessionData('userId'), $this->formData('grade'));
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
            $this->service->editStartOfTraining($this->sessionData('userId'), $this->formData('startOfTraining'));
            $this->redirect('/user/profile');
        }

        $errorMessages[] = $this->getErrorMessageForErrorCode($this->requestValidator->errorCodes()['startOfTraining']);

        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->hideInfos = true;

        $profileView = $this->view('app/views/ProfileView.php');
        $profileView->user = $this->service->findUserById($this->sessionData('userId'));
        $profileView->errorMessages = $errorMessages;

        $footerView = $this->view('app/views/Footer.php');

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
            $this->service->editTrainingYear($this->sessionData('userId'), $this->formData('trainingYear'));
            $this->redirect('/user/profile');
        }
        $errorCodes = $this->requestValidator->errorCodes();
        $errorMessages[] = $this->getErrorMessageForErrorCode($errorCodes['trainingYear']);

        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->hideInfos = true;

        $profileView = $this->view('app/views/ProfileView.php');
        $profileView->user = $this->service->findUserById($this->sessionData('userId'));
        $profileView->errorMessages = $errorMessages;

        $footerView = $this->view('app/views/Footer.php');

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

        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->hideInfos = true;

        $changePasswordView = $this->view('app/views/ChangePasswordView.php');

        $footerView = $this->view('app/views/Footer.php');

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
                $exceptions[] = 'Die eingegebenen Passwörter stimmen nicht überein!';
            }
        } else {
            $this->redirect("/user");
        }

        if ($exceptions !== []) {
            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->hideInfos = true;

            $changePasswordView = $this->view('app/views/ChangePasswordView.php');
            $changePasswordView->errorMessages = $exceptions;

            $footerView = $this->view('app/views/Footer.php');

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
        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->hideInfos = true;

        $viewProfileView = $this->view('app/views/UserProfileView.php');
        $viewProfileView->user = $this->service->findUserById($this->queryParams('userId'));

        $footerView = $this->view('app/views/Footer.php');
        $footerView->backButton = 'show';

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
            case User::ERR_PASSWORD_LENGTH:
                return 'Das Passwort muss mindestens ' . User::PASSWORD_LENGTH . ' Zeichen lang sein!' . "\n";

            case User::ERR_PASSWORD_NOT_NEW:
                return 'Das neue Passwort muss anders als das derzeitige Passwort sein!' . "\n";

            case User::ERR_PASSWORD_WRONG:
                return 'Das derzeitige Passwort ist falsch!' . "\n";

            case UserService::ERR_USERNAME_EXISTS:
                return 'Der Benutzername existiert bereits!' . "\n";

            case UserService::ERR_EMAIL_EXISTS:
                return 'Die E-Mail existiert bereits!' . "\n";

            case Validator::ERR_VALIDATOR_DATE:
                return 'Der eingegebene Wert ist kein Datum!' . "\n";

            case Validator::ERR_VALIDATOR_INT:
                return 'Der eingegebene Wert ist keine Zahl!' . "\n";
        }
    }
}
