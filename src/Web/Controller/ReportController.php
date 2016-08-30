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
        isAuthorized('Trainee');

        $reportRepository = new ReportFileRepository('../reports');
        $service = new ReportBookService($reportRepository);

        $reports = $service->findByTraineeId(session('userId'));

        $headerView = new View('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = new View('app/views/Infobar.php');
        $infobarView->infoHeadline = 'Berichtsheft';

        $reportView = new View('app/views/TraineeView.php');
        $reportView->reports = $reports;

        $footerView = new View('app/views/Footer.php');
        $footerView->backButton = 'nope';

        echo $infobarView->render();
        echo $headerView->render();
        echo $reportView->render();
        echo $footerView->render();
    }

    public function listAction()
    {
        if (isAuthorized('Trainee')) {

        } elseif (isAuthorized('Trainer')) {
            $reportRepository = new ReportFileRepository('reports');
            $service = new ReportBookService($reportRepository);

            $reports = array_merge(
                $service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
                $service->findByStatus(Report::STATUS_APPROVED),
                $service->findByStatus(Report::STATUS_DISAPPROVED)
            );

            $headerView = new View('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = new View('app/views/Infobar.php');
            $infobarView->infoHeadline = 'Berichte der Azubis';

            $reportView = new View('app/views/TrainerView.php');
            $reportView->reports = $reports;

            $footerView = new View('app/views/Footer.php');
            $footerView->backButton = 'nope';

            echo $infobarView->render();
            echo $headerView->render();
            echo $reportView->render();
            echo $footerView->render();
        } else {
            header("Location: /user");
        }
    }
}
