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
        if ($this->formData('email') !== null) {

            $email = $this->formData('email');
            $password = $this->formData('password');

            if ($this->service->authUser($email, $password)) {
                $user = $this->service->findUserByEmail($email);

                $role = $_SESSION['role'] = $user->roleName();
                $_SESSION['authorized'] = true;
                $_SESSION['userId'] = $user->id();
                $_SESSION['username'] = $user->forename();

                $this->redirect('/report/list');

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

                $this->service->registerTrainer($forename, $surname, $email, $password);
                header("Location: /user");

            } elseif ($role === 'TRAINEE') {

                $this->service->registerTrainee($forename, $surname, $email, $password);
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

    public function approveAction()
    {
        if ($this->isAuthorized('TRAINER')) {
            $this->service->approveRole($this->queryParams('email'));
            $this->redirect("/user/userlist");
        } else {
            $this->redirect("/user");
        }
    }

    public function disapproveAction()
    {
        if ($this->isAuthorized('TRAINER')) {
            $this->service->disapproveRole($this->queryParams('email'));
            $this->redirect("/user/userlist");
        } else {
            $this->redirect("/user");
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
