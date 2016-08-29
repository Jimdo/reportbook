<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainee');

$traineeId = session('userId');

$reportView = new Web\View('views/Report.php');
$reportView->title = 'Bericht';
$reportView->action = 'ReportActionProcessor.php';
$reportView->legend = 'Neuen Bericht erstellen';
$reportView->calendarWeek = date('W');
$reportView->date = date('d.m.Y');
$reportView->content = '';
$reportView->buttonName = 'Bericht erstellen';
$reportView->reportId = null;
$reportView->backButton = 'show';
$reportView->backAction = 'trainee.php';
$reportView->reportAction = 'create';
$reportView->role = 'Trainee';

echo $reportView->render();
?>
