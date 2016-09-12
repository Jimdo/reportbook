<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\User as User;
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
        $username = $this->formData('username');
        $password = $this->formData('password');

        $role = '';

        if (
            !(($username === 'Jenny' && $password === 'jenny123') ||
             ($username === 'Tom' && $password === 'tom123') ||
             ($username === 'Hauke' && $password === 'hauke123'))
        ) {
            header("Location: /user");
        } else {
            $_SESSION['username'] = $username;

            if ($username === 'Jenny') {
                $traineeId = $_SESSION['userId'] = 'jennyxyz';
                $role = $_SESSION['role'] = 'Trainee';
            }

            if ($username === 'Tom') {
                $traineeId = $_SESSION['userId'] = 'tomxyz';
                $role = $_SESSION['role'] = 'Trainee';
            }

            if ($username === 'Hauke') {
                $_SESSION['userId'] = 'haukexyz';
                $role = $_SESSION['role'] = 'Trainer';
            }

            $_SESSION['authorized'] = true;

            switch ($role) {
                case 'Trainee':
                    header("Location: /report/list");
                    break;
                case 'Trainer':
                    header("Location: /report/list");
                    break;
                default:
                    header("Location: /user");
                    $_SESSION['authorized'] = false;
                    break;
            }
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
            if ($role === 'trainer') {

                $this->service->registerTrainer($forename, $surname, $email, $password);
                header("Location: /user");

            } elseif ($role === 'trainee') {

                $this->service->registerTrainee($forename, $surname, $email, $password);
                header("Location: /user");

            } else {
                header("Location: /user");
            }
        }

    }

    public function userlistAction()
    {
        if ($this->isAuthorized('Trainer')) {
            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->infoHeadline = ' | Benutzeranfragen';

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            $userView = $this->view('app/views/UserlistView.php');
            $userView->users = $this->service->findUserByStatus(User::STATUS_NOT_APPROVED);

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

    public function logoutAction()
    {
        $_SESSION['authorized'] = false;
        $_SESSION['userId'] = '';
        $_SESSION['role'] = '';

        header("Location: /user");
    }
}
