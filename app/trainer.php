<?php
namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainer');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$reports = array_merge(
    $service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
    $service->findByStatus(Report::STATUS_APPROVED),
    $service->findByStatus(Report::STATUS_DISAPPROVED)
);

$headerView = new Web\View('views/Header.php');
$headerView->tabTitle = 'Berichtsheft';

$infobarView = new Web\View('views/Infobar.php');
$infobarView->infoHeadline = 'Berichte der Azubis';

$reportView = new Web\View('views/TrainerView.php');
$reportView->reports = $reports;

$footerView = new Web\View('views/Footer.php');
$footerView->backButton = 'nope';

echo $infobarView->render();
echo $headerView->render();
echo $reportView->render();
echo $footerView->render();
