<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\User as User;
use Jimdo\Reports\Role as Role;
use Jimdo\Reports\UserService as UserService;
use Jimdo\Reports\UserFileRepository as UserFileRepository;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;

class UserController extends Controller
{
    /** @var UserService */
    private $service;

    /**
     * @param Request $request
     */
    public function __construct(Request $request, RequestValidator $requestValidator)
    {
        parent::__construct($request, $requestValidator);

        $userRepository = new UserFileRepository('users');
        $this->service = new UserService($userRepository);
    }


    public function indexAction()
    {
        $headerView = $this->view('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $loginView = $this->view('app/views/LoginView.php');
        $footerView = $this->view('app/views/Footer.php');

        $footerView->backButton = 'nope';

        echo $headerView->render();
        echo $loginView->render();
        echo $footerView->render();
    }

    public function loginAction()
    {
        if ($this->formData('identifier') !== null) {

            $identifier = $this->formData('identifier');
            $password = $this->formData('password');

            if ($identifier === 'admin' && $password === 'adminadmin') {
                if (!file_exists('users')) {
                    $this->service->ensureUsersPath();
                    $adminUser = $this->service->registerTrainer('admin', 'admin', 'admin', 'admin', 'adminadmin');
                    $this->service->approveRole($adminUser->email());
                }
            }
            if ($this->service->authUser($identifier, $password)) {
                if ($this->service->findUserByEmail($identifier) !== null) {
                    $user = $this->service->findUserByEmail($identifier);
                } elseif ($this->service->findUserByUsername($identifier) !== null) {
                    $user = $this->service->findUserByUsername($identifier);
                }

                $role = $_SESSION['role'] = $user->roleName();
                $_SESSION['authorized'] = true;
                $_SESSION['userId'] = $user->id();
                $_SESSION['username'] = $user->forename();

                if ($identifier === 'admin' && $password === 'adminadmin') {
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

        echo $headerView->render();
        echo $registerView->render();
        echo $footerView->render();
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

            echo $headerView->render();
            echo $registerView->render();
            echo $footerView->render();

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
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->infoHeadline = ' | Benutzeranfragen';

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            $userView = $this->view('app/views/UserlistView.php');
            $userView->users = $this->service->findUsersByStatus(Role::STATUS_NOT_APPROVED);

            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            echo $headerView->render();
            echo $infobarView->render();
            echo $userView->render();
            echo $footerView->render();

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
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');

        $changePasswordView = $this->view('app/views/ChangePasswordView.php');

        $footerView = $this->view('app/views/Footer.php');

        echo $headerView->render();
        echo $infobarView->render();
        echo $changePasswordView->render();
        echo $footerView->render();
    }

    public function editPasswordAction()
    {
        if ($this->isAuthorized('TRAINER') || $this->isAuthorized('TRAINEE')) {

            if ($this->formData('newPassword') === $this->formData('passwordConfirmation')) {
                $this->service->editPassword($this->sessionData('userId'), $this->formData('currentPassword'), $this->formData('newPassword'));
                $this->redirect("/report/list");
            } else {
                $headerView = $this->view('app/views/Header.php');
                $headerView->tabTitle = 'Berichtsheft';

                $infobarView = $this->view('app/views/Infobar.php');
                $infobarView->username = $this->sessionData('username');
                $infobarView->role = $this->sessionData('role');

                $changePasswordView = $this->view('app/views/ChangePasswordView.php');
                $changePasswordView->errorMessages = ['Die eingegebenen Passwörter stimmen nicht überein'];

                $footerView = $this->view('app/views/Footer.php');

                echo $headerView->render();
                echo $infobarView->render();
                echo $changePasswordView->render();
                echo $footerView->render();
            }

        }
    }

    public function logoutAction()
    {
        $_SESSION['authorized'] = false;
        $_SESSION['userId'] = '';
        $_SESSION['role'] = '';

        $this->redirect("/user");
    }
}
