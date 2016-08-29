<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

$_SESSION['authorized'] = false;

$username = $_POST['username'];
$password = $_POST['password'];
$role = '';

if (
    !(($username === 'Jenny' && $password === 'jenny123') ||
     ($username === 'Tom' && $password === 'tom123') ||
     ($username === 'Hauke' && $password === 'hauke123'))
) {
    redirect('login.php');
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
            redirect('trainee.php');
            break;
        case 'Trainer':
            redirect('trainer.php');
            break;
        default:
            redirect('login.php');
            $_SESSION['authorized'] = false;
            break;
    }
}
