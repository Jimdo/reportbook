<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainee');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$service->deleteReport(get('report_Id'));

redirect('trainee.php');
