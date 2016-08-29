<?php

namespace Jimdo\Reports;

use \Jimdo\Reports\Report as Report;

require 'bootstrap.php';

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$reportId = post('reportId');
$traineeId = session('userId');
$content = post('content');
$date = post('date');
$calendarWeek = post('calendarWeek');

switch (request('reportAction')) {
    case 'edit':
        $requestValidator = new Web\RequestValidator();
        //$requestValidator->add('userId', 'string');
        $requestValidator->add('content', 'string');
        $requestValidator->add('date', 'date');
        $requestValidator->add('calendarWeek', 'integer');

        if ($requestValidator->isValid($_REQUEST)) {
            $service->editReport($reportId, $content, $date, $calendarWeek);
        } else {
            $reportView = new Web\View('views/Report.php');
            $reportView->errorMessages = $requestValidator->errorMessages();
            $reportView->title = 'Bericht';
            $reportView->action = 'ReportActionProcessor.php';
            $reportView->legend = 'Bericht bearbeiten';
            $reportView->calendarWeek = $calendarWeek;
            $reportView->date = $date;
            $reportView->content = $content;
            $reportView->buttonName = 'Speichern';
            $reportView->reportId = $reportId;
            $reportView->backButton = 'show';
            $reportView->backAction = 'trainee.php';
            $reportView->reportAction = 'edit';
            $reportView->role = 'Trainee';

            echo $reportView->render();
            exit;
        }

        break;
    case 'create':
        $requestValidator = new Web\RequestValidator();
        //$requestValidator->add('userId', 'string');
        $requestValidator->add('content', 'string');
        $requestValidator->add('date', 'date');
        $requestValidator->add('calendarWeek', 'integer');


        if ($requestValidator->isValid($_REQUEST)) {
            $service->createReport($traineeId, $content, $date, $calendarWeek);
        } else {
            $reportView = new Web\View('views/Report.php');
            $reportView->errorMessages = $requestValidator->errorMessages();
            $reportView->title = 'Bericht';
            $reportView->action = 'ReportActionProcessor.php';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = $calendarWeek;
            $reportView->date = $date;
            $reportView->content = $content;
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->backButton = 'show';
            $reportView->backAction = 'trainee.php';
            $reportView->reportAction = 'create';
            $reportView->role = 'Trainee';

            echo $reportView->render();
            exit;
        };

        break;
    case 'approve':
        $service->approveReport($reportId);
        break;
    case 'disapprove':
        $service->disapproveReport($reportId);
        break;
    default:
        break;
}

if (get('action') === 'delete' && $service->findById(get('reportId'), $traineeId)->status() !== Report::STATUS_DISAPPROVED) {
    $service->deleteReport(get('reportId'));
} elseif (get('action') === 'requestApproval') {
    $service->requestApproval(get('reportId'));
}

if (session('role') === 'Trainee') {
    redirect('trainee.php');
} elseif (session('role') === 'Trainer') {
    redirect('trainer.php');
}
