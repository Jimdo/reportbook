<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Report as Report;
use Jimdo\Reports\ReportFileRepository as ReportFileRepository;
use Jimdo\Reports\ReportBookService as ReportBookService;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\Request as Request;

class ReportController extends Controller
{
    /** @var ReportBookService */
    private $service;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $reportRepository = new ReportFileRepository('reports');
        $this->service = new ReportBookService($reportRepository);
    }

    public function indexAction()
    {
        header("Location: /report/list");
    }

    public function listAction()
    {
        $headerView = new View('app/views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = new View('app/views/Infobar.php');

        $footerView = new View('app/views/Footer.php');
        $footerView->backButton = 'nope';

        if (isAuthorized('Trainee')) {

            $reportView = new View('app/views/TraineeView.php');

            $reportView->reports = $this->service->findByTraineeId(session('userId'));

            $infobarView->infoHeadline = 'Berichtsheft';

        } elseif (isAuthorized('Trainer')) {

            $reportView = new View('app/views/TrainerView.php');

            $reportView->reports = array_merge(
                $this->service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
                $this->service->findByStatus(Report::STATUS_APPROVED),
                $this->service->findByStatus(Report::STATUS_DISAPPROVED)
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
            $reportView->role = 'Trainee';

            echo $reportView->render();
        }
    }

    public function createAction()
    {
        $requestValidator = new RequestValidator();
        // $requestValidator->add('userId', 'string');
        $requestValidator->add('content', 'string');
        $requestValidator->add('date', 'date');
        $requestValidator->add('calendarWeek', 'integer');

        if ($requestValidator->isValid($_REQUEST)) {
            $this->service->createReport(
                session('userId')
                , $this->formData('content')
                , $this->formData('date')
                , $this->formData('calendarWeek')
            );

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
            $reportView->role = 'Trainee';

            echo $reportView->render();
        }
    }

    public function editReportAction()
    {
        if (isAuthorized('Trainee')) {
            $reportId = $this->queryParams('reportId');
            $report = $this->service->findById($reportId, session('userId'));

            $reportView = new View('app/views/Report.php');
            $reportView->title = 'Bericht';
            $reportView->action = '/report/edit';
            $reportView->legend = 'Bericht bearbeiten';
            $reportView->calendarWeek = $report->calendarWeek();
            $reportView->date = $report->date();
            $reportView->content = $report->content();
            $reportView->buttonName = 'Speichern';
            $reportView->reportId = $reportId;
            $reportView->backButton = 'show';
            $reportView->role = 'Trainee';

            echo $reportView->render();
        }
    }

    public function editAction()
    {
        if (isAuthorized('Trainee')) {

        $requestValidator = new RequestValidator();
        //$requestValidator->add('userId', 'string');
        $requestValidator->add('content', 'string');
        $requestValidator->add('date', 'date');
        $requestValidator->add('calendarWeek', 'integer');

            if ($requestValidator->isValid($_REQUEST)) {
                $this->service->editReport(
                      $this->formData('reportId')
                    , $this->formData('content')
                    , $this->formData('date')
                    , $this->formData('calendarWeek')
                );

                header("Location: /report/list");

            } else {
                $reportView = new View('app/views/Report.php');
                $reportView->errorMessages = $requestValidator->errorMessages();
                $reportView->title = 'Bericht';
                $reportView->action = '/report/editReport';
                $reportView->legend = 'Bericht bearbeiten';
                $reportView->calendarWeek = $calendarWeek;
                $reportView->date = $date;
                $reportView->content = $content;
                $reportView->buttonName = 'Speichern';
                $reportView->reportId = $reportId;
                $reportView->backButton = 'show';
                $reportView->role = 'Trainee';

                echo $reportView->render();
            }
        }
    }

    public function requestApprovalAction()
    {
        if (isAuthorized('Trainee')) {
            $this->service->requestApproval($this->queryParams('reportId'));
            header("Location: /report/list");
        }
    }
}
