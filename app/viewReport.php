<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainer');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$reportId = get('reportId');
$traineeId = get('traineeId');
$report = $service->findById($reportId, $traineeId);

$reportView = new Web\View('views/Report.php');
$reportView->title = 'Bericht';
$reportView->action = 'ReportActionProcessor.php';
$reportView->legend = 'Vorschau';
$reportView->calendarWeek = $report->calendarWeek();
$reportView->date = $report->date();
$reportView->content = $report->content();
$reportView->buttonName = 'Speichern';
$reportView->reportId = $reportId;
$reportView->backButton = 'show';
$reportView->backAction = 'trainer.php';
$reportView->readonly = 'readonly';
$reportView->role = 'Trainer';
$reportView->status = $report->status();

echo $reportView->render();
