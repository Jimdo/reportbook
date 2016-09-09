<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;

class UserController extends Controller
{
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

    public function logoutAction()
    {
        $_SESSION['authorized'] = false;
        $_SESSION['userId'] = '';
        $_SESSION['role'] = '';

        header("Location: /user");
    }
}
