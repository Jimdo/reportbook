<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainee');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

if (get('report_Id') !== null) {
    $service->editReport(get('report_Id'), post('content'), post('date'), post('calendarWeek'));
} else {
    $traineeId = session('userId');
    $content = post('content');
    $date = post('date');
    $calendarWeek = post('calendarWeek');
    $service->createReport($traineeId, $content, $date, $calendarWeek);
}

redirect('trainee.php');
