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
    public function __construct(Request $request, RequestValidator $requestValidator)
    {
        parent::__construct($request, $requestValidator);

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
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('app/views/Infobar.php');
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');

        $footerView = $this->view('app/views/Footer.php');
        $footerView->backButton = 'nope';

        if ($this->isAuthorized('Trainee')) {

            $reportView = $this->view('app/views/TraineeView.php');

            $reportView->reports = $this->service->findByTraineeId($this->sessionData('userId'));

        } elseif ($this->isAuthorized('Trainer')) {

            $reportView = $this->view('app/views/TrainerView.php');

            $reportView->reports = array_merge(
                $this->service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
                $this->service->findByStatus(Report::STATUS_APPROVED),
                $this->service->findByStatus(Report::STATUS_DISAPPROVED)
            );

            $infobarView->infoHeadline = 'Berichte der Azubis';
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');


        } else {

            $this->redirect("/user");

        }
        echo $headerView->render();
        echo $infobarView->render();
        echo $reportView->render();
        echo $footerView->render();
    }

    public function createReportAction()
    {
        if ($this->isAuthorized('Trainee')) {
            $traineeId = $this->sessionData('userId');

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

            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'nope';

            echo $headerView->render();
            echo $infobarView->render();
            echo $reportView->render();
            echo $footerView->render();
        }
    }

    public function createAction()
    {
        $this->addRequestValidation('content', 'string');
        $this->addRequestValidation('date', 'date');
        $this->addRequestValidation('calendarWeek', 'integer');

        if ($this->isRequestValid()) {
            $this->service->createReport(
                $this->sessionData('userId')
                , $this->formData('content')
                , $this->formData('date')
                , $this->formData('calendarWeek')
            );

            $this->redirect("/report/list");

        } else {
            $reportView = $this->view('app/views/Report.php');
            $reportView->errorMessages = $this->requestValidator->errorMessages();
            $reportView->title = 'Bericht';
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = $this->formData('calendarWeek');
            $reportView->date = $this->formData('date');
            $reportView->content = $this->formData('content');
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->backButton = 'show';
            $reportView->role = 'Trainee';


            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'nope';

            echo $headerView->render();
            echo $infobarView->render();
            echo $reportView->render();
            echo $footerView->render();
        }
    }

    public function editReportAction()
    {
        if ($this->isAuthorized('Trainee')) {
            $reportId = $this->queryParams('reportId');
            $report = $this->service->findById($reportId, $this->sessionData('userId'));

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

            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'nope';

            echo $headerView->render();
            echo $infobarView->render();
            echo $reportView->render();
            echo $footerView->render();
        }
    }

    public function editAction()
    {
        if ($this->isAuthorized('Trainee')) {

        $this->addRequestValidation('content', 'string');
        $this->addRequestValidation('date', 'date');
        $this->addRequestValidation('calendarWeek', 'integer');

            if ($this->isRequestValid()) {
                $this->service->editReport(
                      $this->formData('reportId')
                    , $this->formData('content')
                    , $this->formData('date')
                    , $this->formData('calendarWeek')
                );

                $this->redirect("/report/list");

            } else {
                $reportView = $this->view('app/views/Report.php');
                $reportView->errorMessages = $this->requestValidator->errorMessages();
                $reportView->title = 'Bericht';
                $reportView->action = '/report/edit';
                $reportView->legend = 'Bericht bearbeiten';
                $reportView->calendarWeek = $this->formData('calendarWeek');
                $reportView->date = $this->formData('date');
                $reportView->content = $this->formData('content');
                $reportView->buttonName = 'Speichern';
                $reportView->reportId = $this->formData('reportId');
                $reportView->backButton = 'show';
                $reportView->role = 'Trainee';

                $headerView = $this->view('app/views/Header.php');
                $headerView->tabTitle = 'Berichtsheft';

                $infobarView = $this->view('app/views/Infobar.php');
                $infobarView->username = $this->sessionData('username');
                $infobarView->role = $this->sessionData('role');

                $footerView = $this->view('app/views/Footer.php');
                $footerView->backButton = 'nope';

                echo $headerView->render();
                echo $infobarView->render();
                echo $reportView->render();
                echo $footerView->render();
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
        if ($this->isAuthorized('Trainee') && $this->service->findById($this->queryParams('reportId'), $this->sessionData('userId'))->status() !== Report::STATUS_DISAPPROVED) {
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

            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'nope';

            echo $headerView->render();
            echo $infobarView->render();
            echo $reportView->render();
            echo $footerView->render();
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
