<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Report as Report;
use Jimdo\Reports\ReportFileRepository as ReportFileRepository;
use Jimdo\Reports\ReportBookService as ReportBookService;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\UserService as UserService;
use Jimdo\Reports\UserFileRepository as UserFileRepository;


class ReportController extends Controller
{
    /** @var ReportBookService */
    private $service;

    /** @var UserService */
    private $userService;

    /**
     * @param Request $request
     */
    public function __construct(Request $request, RequestValidator $requestValidator)
    {
        parent::__construct($request, $requestValidator);

        $reportRepository = new ReportFileRepository('reports');
        $this->service = new ReportBookService($reportRepository);

        $userRepository = new UserFileRepository('users');
        $this->userService = new UserService($userRepository);
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
        $infobarView->infoHeadline = ' | Übersicht';

        $footerView = $this->view('app/views/Footer.php');
        $footerView->backButton = 'nope';

        if ($this->isAuthorized('TRAINEE')) {

            $reportView = $this->view('app/views/TraineeView.php');
            $reportView->reports = $this->service->findByTraineeId($this->sessionData('userId'));

        } elseif ($this->isAuthorized('TRAINER')) {

            $reportView = $this->view('app/views/TrainerView.php');
            $reportView->userService = $this->userService;
            $reportView->reports = array_merge(
                $this->service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
                $this->service->findByStatus(Report::STATUS_APPROVED),
                $this->service->findByStatus(Report::STATUS_DISAPPROVED),
                $this->service->findByStatus(Report::STATUS_REVISED)
            );

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
        if ($this->isAuthorized('TRAINEE')) {
            $traineeId = $this->sessionData('userId');

            $reportView = $this->view('app/views/Report.php');
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = date('W');
            $reportView->date = date('d.m.Y');
            $reportView->content = '';
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->reportId = null;
            $reportView->backButton = 'show';
            $reportView->role = 'TRAINEE';

            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

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
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = $this->formData('calendarWeek');
            $reportView->date = $this->formData('date');
            $reportView->content = $this->formData('content');
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->backButton = 'show';
            $reportView->role = 'TRAINEE';

            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            echo $headerView->render();
            echo $infobarView->render();
            echo $reportView->render();
            echo $footerView->render();
        }
    }

    public function editReportAction()
    {
        if ($this->isAuthorized('TRAINEE')) {
            $reportId = $this->formData('reportId');
            $report = $this->service->findById($reportId, $this->sessionData('userId'));

            $reportView = $this->view('app/views/Report.php');
            $reportView->action = '/report/edit';
            $reportView->legend = 'Bericht bearbeiten';
            $reportView->calendarWeek = $report->calendarWeek();
            $reportView->date = $report->date();
            $reportView->content = $report->content();
            $reportView->buttonName = 'Speichern';
            $reportView->reportId = $reportId;
            $reportView->backButton = 'show';
            $reportView->role = 'TRAINEE';

            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            echo $headerView->render();
            echo $infobarView->render();
            echo $reportView->render();
            echo $footerView->render();
        }
    }

    public function editAction()
    {
        if ($this->isAuthorized('TRAINEE')) {

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
                $reportView->action = '/report/edit';
                $reportView->legend = 'Bericht bearbeiten';
                $reportView->calendarWeek = $this->formData('calendarWeek');
                $reportView->date = $this->formData('date');
                $reportView->content = $this->formData('content');
                $reportView->buttonName = 'Speichern';
                $reportView->reportId = $this->formData('reportId');
                $reportView->backButton = 'show';
                $reportView->role = 'TRAINEE';

                $headerView = $this->view('app/views/Header.php');
                $headerView->tabTitle = 'Berichtsheft';

                $infobarView = $this->view('app/views/Infobar.php');
                $infobarView->username = $this->sessionData('username');
                $infobarView->role = $this->sessionData('role');

                $footerView = $this->view('app/views/Footer.php');
                $footerView->backButton = 'show';

                echo $headerView->render();
                echo $infobarView->render();
                echo $reportView->render();
                echo $footerView->render();
            }
        }
    }

    public function deleteReportAction()
    {
        if ($this->isAuthorized('TRAINEE') && $this->service->findById($this->formData('reportId'), $this->sessionData('userId'))->status() !== Report::STATUS_DISAPPROVED) {
            $this->service->deleteReport($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function requestApprovalAction()
    {
        if ($this->isAuthorized('TRAINEE')) {
            $this->service->requestApproval($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }

    function viewReportAction()
    {
        if ($this->isAuthorized('TRAINER') || $this->isAuthorized('TRAINEE')) {
            $report = $this->service->findById($this->formData('reportId'), $this->formData('traineeId'));

            $reportView = $this->view('app/views/Report.php');
            $reportView->title = 'Bericht';
            $reportView->legend = 'Vorschau';
            $reportView->calendarWeek = $report->calendarWeek();
            $reportView->date = $report->date();
            $reportView->content = $report->content();
            $reportView->buttonName = 'Speichern';
            $reportView->reportId = $this->formData('reportId');
            $reportView->backButton = 'show';
            $reportView->readonly = 'readonly';
            $reportView->role = $this->sessionData('role');
            $reportView->status = $report->status();

            $headerView = $this->view('app/views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('app/views/Infobar.php');
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            echo $headerView->render();
            echo $infobarView->render();
            echo $reportView->render();
            echo $footerView->render();
        }
    }

    public function approveReportAction()
    {
        if ($this->isAuthorized('TRAINER')) {
            $this->service->approveReport($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function disapproveReportAction()
    {
        if ($this->isAuthorized('TRAINER')) {
            $this->service->disapproveReport($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }
}
