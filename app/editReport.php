<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainee');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$reportId = get('reportId');
$report = $service->findById($reportId, session('userId'));

$reportView = new Web\View('views/Report.php');
$reportView->title = 'Bericht';
$reportView->action = 'ReportActionProcessor.php';
$reportView->legend = 'Bericht bearbeiten';
$reportView->calendarWeek = $report->calendarWeek();
$reportView->date = $report->date();
$reportView->content = $report->content();
$reportView->buttonName = 'Speichern';
$reportView->reportId = $reportId;
$reportView->backButton = 'show';
$reportView->backAction = 'trainee.php';
$reportView->reportAction = 'edit';
$reportView->role = 'Trainee';

echo $reportView->render();
?>
