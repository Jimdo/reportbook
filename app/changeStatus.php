<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainer');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);
$reportId = post('report_Id');

if (post('status') === 'approve') {
    $service->approveReport($reportId);
} elseif (post('status') === 'disapprove') {
    $service->disapproveReport($reportId);
}

redirect('trainer.php');
