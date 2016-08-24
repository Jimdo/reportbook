<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

$_SESSION['authorized'] = false;
$_SESSION['userId'] = '';
$_SESSION['role'] = '';

function redirect(string $target) {
    header("Location: http://localhost:8000/$target");
}

redirect('login.php');
