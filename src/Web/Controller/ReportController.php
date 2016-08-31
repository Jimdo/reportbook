<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;

use Jimdo\Reports\Report as Report;

use Jimdo\Reports\ReportFileRepository as ReportFileRepository;

use Jimdo\Reports\ReportBookService as ReportBookService;

use Jimdo\Reports\Web\RequestValidator as RequestValidator;

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

    public function createReportAction()
    {
        if (isAuthorized('Trainee')) {
            $traineeId = session('userId');

            $reportView = new View('app/views/Report.php');
            $reportView->title = 'Bericht';
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = date('W');
            $reportView->date = date('d.m.Y');
            $reportView->content = '';
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->reportId = null;
            $reportView->backButton = 'show';
            $reportView->backAction = '/report/list';
            $reportView->role = 'Trainee';

            echo $reportView->render();
        }
    }

    public function createAction()
    {
        $reportRepository = new ReportFileRepository('reports');
        $service = new ReportBookService($reportRepository);

        $requestValidator = new RequestValidator();
        // $requestValidator->add('userId', 'string');
        $requestValidator->add('content', 'string');
        $requestValidator->add('date', 'date');
        $requestValidator->add('calendarWeek', 'integer');

        $traineeId = session('userId');
        $content = $this->formData('content');
        $date = $this->formData('date');
        $calendarWeek = $this->formData('calendarWeek');

        if ($requestValidator->isValid($_REQUEST)) {
            $service->createReport($traineeId, $content, $date, $calendarWeek);
            header("Location: /report/list");
        } else {
            $reportView = new View('app/views/Report.php');
            $reportView->errorMessages = $requestValidator->errorMessages();
            $reportView->title = 'Bericht';
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = $calendarWeek;
            $reportView->date = $date;
            $reportView->content = $content;
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->backButton = 'show';
            $reportView->backAction = '/report/list';
            $reportView->role = 'Trainee';

            echo $reportView->render();
        }
    }
}
