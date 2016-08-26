<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);
$reportId = post('reportId');

switch (request('reportAction')) {
    case 'edit':
        $service->editReport($reportId, post('content'), post('date'), post('calendarWeek'));
        break;
    case 'create':
        $requestValidator = new Web\RequestValidator();
        //$requestValidator->add('userId', 'string');
        $requestValidator->add('content', 'string');
        $requestValidator->add('date', 'date');
        $requestValidator->add('calendarWeek', 'integer');

        $traineeId = session('userId');
        $content = post('content');
        $date = post('date');
        $calendarWeek = post('calendarWeek');

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
            $reportView->reportId = null;
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

if (get('action') === 'delete') {
    $service->deleteReport(get('reportId'));
} elseif (get('action') === 'requestApproval') {
    $service->requestApproval(get('reportId'));
}

if (session('role') === 'Trainee') {
    redirect('trainee.php');
} elseif (session('role') === 'Trainer') {
    redirect('trainer.php');
}
