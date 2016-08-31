<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;

use Jimdo\Reports\Report as Report;

use Jimdo\Reports\ReportFileRepository as ReportFileRepository;

use Jimdo\Reports\ReportBookService as ReportBookService;

class ReportController extends Controller
{
    public function indexAction()
    {
        header("Location: /report/list");
    }

    public function listAction()
    {
        $reportRepository = new ReportFileRepository('reports');
        $service = new ReportBookService($reportRepository);

        $headerView = new View('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = new View('app/views/Infobar.php');

        $footerView = new View('app/views/Footer.php');
        $footerView->backButton = 'nope';

        if (isAuthorized('Trainee')) {

            $reportView = new View('app/views/TraineeView.php');

            $reportView->reports = $service->findByTraineeId(session('userId'));

            $infobarView->infoHeadline = 'Berichtsheft';

        } elseif (isAuthorized('Trainer')) {

            $reportView = new View('app/views/TrainerView.php');

            $reportView->reports = array_merge(
                $service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
                $service->findByStatus(Report::STATUS_APPROVED),
                $service->findByStatus(Report::STATUS_DISAPPROVED)
            );

            $infobarView->infoHeadline = 'Berichte der Azubis';

        } else {

            header("Location: /user");

        }
        echo $infobarView->render();
        echo $headerView->render();
        echo $reportView->render();
        echo $footerView->render();
    }
}
