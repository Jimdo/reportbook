<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

$loginView = new Web\View('views/LoginView.php');
$footerView = new Web\View('views/Footer.php');

$footerView->backButton = 'nope';

echo $loginView->render();
echo $footerView->render();
