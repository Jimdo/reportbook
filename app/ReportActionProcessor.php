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
        $traineeId = session('userId');
        $content = post('content');
        $date = post('date');
        $calendarWeek = post('calendarWeek');
        $service->createReport($traineeId, $content, $date, $calendarWeek);
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
