<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Web\ViewHelper as ViewHelper;
use Jimdo\Reports\User as User;
use Jimdo\Reports\Role as Role;
use Jimdo\Reports\UserService as UserService;
use Jimdo\Reports\UserMongoRepository as UserMongoRepository;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\PasswordException as PasswordException;
use Jimdo\Reports\Serializer as Serializer;

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
    public function __construct(Request $request, RequestValidator $requestValidator, ApplicationConfig $appConfig, Response $response)
    {
        parent::__construct($request, $requestValidator, $appConfig, $response);

        $uri = 'mongodb://' . $appConfig->mongoIp . ':27017';
        $client = new \MongoDB\Client($uri);

        $userRepository = new UserMongoRepository($client, new Serializer(), $appConfig);
        $this->service = new UserService($userRepository);
        $this->viewHelper = new ViewHelper();
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
                $adminUser = $this->service->registerTrainer('admin', 'admin', self::ADMIN_DEFAULT_USER, 'admin', self::ADMIN_DEFAULT_PASSWORD);
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
                $_SESSION['username'] = $user->forename();

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
        if ($this->isAuthorized('TRAINER')) {
            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->infoHeadline = ' | Benutzeranfragen';

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
        if ($this->isAuthorized('TRAINER')) {

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

    public function changePasswordAction()
    {
        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');

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


            if ($this->isAuthorized('TRAINER') || $this->isAuthorized('TRAINEE')) {

                if ($this->formData('newPassword') === $this->formData('passwordConfirmation')) {

                    try {
                        $this->service->editPassword($this->sessionData('userId'), $this->formData('currentPassword'), $this->formData('newPassword'));
                    } catch (PasswordException $e) {
                        $exceptions[] = $this->getErrorMessageForErrorCode($e->getCode());
                    }

                } else {
                    $exceptions[] = 'Die eingegebenen Passwörter stimmen nicht überein!';
                }
            }


        if ($exceptions !== []) {

            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

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
        }
    }
}
