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
        $this->redirect("/report/list");
    }

    public function listAction()
    {

        $headerView = $this->view('app/views/Header.php');

        // $headerView = $this->view('app/views/Header.php');

        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');

        $footerView = $this->view('app/views/Footer.php');
        $footerView->backButton = 'nope';

        if ($this->isAuthorized('Trainee')) {

            $reportView = $this->view('app/views/TraineeView.php');

            $reportView->reports = $this->service->findByTraineeId(session('userId'));

            $infobarView->infoHeadline = 'Berichtsheft';

        } elseif ($this->isAuthorized('Trainer')) {

            $reportView = $this->view('app/views/TrainerView.php');

            $reportView->reports = array_merge(
                $this->service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
                $this->service->findByStatus(Report::STATUS_APPROVED),
                $this->service->findByStatus(Report::STATUS_DISAPPROVED)
            );

            $infobarView->infoHeadline = 'Berichte der Azubis';

        } else {

            $this->redirect("/user");

        }
        echo $infobarView->render();
        echo $headerView->render();
        echo $reportView->render();
        echo $footerView->render();
    }

    public function createReportAction()
    {
        if ($this->isAuthorized('Trainee')) {
            $traineeId = session('userId');

            $reportView = $this->view('app/views/Report.php');
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

            $this->redirect("/report/list");

        } else {
            $reportView = $this->view('app/views/Report.php');
            $reportView->errorMessages = $requestValidator->errorMessages();
            $reportView->title = 'Bericht';
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = $this->formData('calendarWeek');
            $reportView->date = $this->formData('date');
            $reportView->content = $this->formData('content');
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->backButton = 'show';
            $reportView->role = 'Trainee';

            echo $reportView->render();
        }
    }

    public function editReportAction()
    {
        if ($this->isAuthorized('Trainee')) {
            $reportId = $this->queryParams('reportId');
            $report = $this->service->findById($reportId, session('userId'));

            $reportView = $this->view('app/views/Report.php');
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
        if ($this->isAuthorized('Trainee')) {

        $requestValidator = new RequestValidator();
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

                $this->redirect("/report/list");

            } else {
                $reportView = $this->view('app/views/Report.php');
                $reportView->errorMessages = $requestValidator->errorMessages();
                $reportView->title = 'Bericht';
                $reportView->action = '/report/editReport';
                $reportView->legend = 'Bericht bearbeiten';
                $reportView->calendarWeek = $this->formData('calendarWeek');
                $reportView->date = $this->formData('date');
                $reportView->content = $this->formData('content');
                $reportView->buttonName = 'Speichern';
                $reportView->reportId = $this->queryParams('reportId');
                $reportView->backButton = 'show';
                $reportView->role = 'Trainee';

                echo $reportView->render();
            }
        }
    }

    public function requestApprovalAction()
    {
        if ($this->isAuthorized('Trainee')) {
            $this->service->requestApproval($this->queryParams('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function deleteAction()
    {
        if ($this->isAuthorized('Trainee') && $this->service->findById($this->queryParams('reportId'), session('userId'))->status() !== Report::STATUS_DISAPPROVED) {
            $this->service->deleteReport($this->queryParams('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function viewReportAction()
    {
        if ($this->isAuthorized('Trainer')) {
            $report = $this->service->findById($this->queryParams('reportId'), $this->queryParams('traineeId'));

            $reportView = $this->view('app/views/Report.php');
            $reportView->title = 'Bericht';
            // $reportView->action = '';
            $reportView->legend = 'Vorschau';
            $reportView->calendarWeek = $report->calendarWeek();
            $reportView->date = $report->date();
            $reportView->content = $report->content();
            $reportView->buttonName = 'Speichern';
            $reportView->reportId = $this->queryParams('reportId');
            $reportView->backButton = 'show';
            $reportView->readonly = 'readonly';
            $reportView->role = 'Trainer';
            $reportView->status = $report->status();

            echo $reportView->render();
        }
    }

    public function approveAction()
    {
        if ($this->isAuthorized('Trainer')) {
            $this->service->approveReport($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function disapproveAction()
    {
        if ($this->isAuthorized('Trainer')) {
            $this->service->disapproveReport($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }
}
