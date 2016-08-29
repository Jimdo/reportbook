<?php
namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainee');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$reports = $service->findByTraineeId(session('userId'));

$headerView = new Web\View('views/Header.php');
$headerView->tabTitle = 'Berichtsheft';

$infobarView = new Web\View('views/Infobar.php');
$infobarView->infoHeadline = 'Berichtsheft';

$reportView = new Web\View('views/TraineeView.php');
$reportView->reports = $reports;

$footerView = new Web\View('views/Footer.php');
$footerView->backButton = 'nope';

echo $infobarView->render();
echo $headerView->render();
echo $reportView->render();
echo $footerView->render();
