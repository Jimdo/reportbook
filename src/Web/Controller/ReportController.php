<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Web\ViewHelper as ViewHelper;
use Jimdo\Reports\Report as Report;
use Jimdo\Reports\ReportMongoRepository as ReportMongoRepository;
use Jimdo\Reports\ReportbookService as ReportbookService;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\UserService as UserService;
use Jimdo\Reports\UserMongoRepository as UserMongoRepository;
use Jimdo\Reports\Web\Validator\Validator as Validator;


class ReportController extends Controller
{
    /** @var ReportbookService */
    private $service;

    /** @var UserService */
    private $userService;

    /** @var ViewHelper */
    private $viewHelper;

    /**
     * @param Request $request
     */
    public function __construct(Request $request, RequestValidator $requestValidator, ApplicationConfig $appConfig, Response $response)
    {
        parent::__construct($request, $requestValidator, $appConfig, $response);

        $client = new \MongoDB\Client($appConfig->mongoUri());

        $reportRepository = new ReportMongoRepository($client, new Serializer(), $appConfig);
        $this->service = new ReportbookService($reportRepository);

        $userRepository = new UserMongoRepository($client, new Serializer(), $appConfig);
        $this->userService = new UserService($userRepository);
        $this->viewHelper = new ViewHelper();
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
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->infoHeadline = ' | Übersicht';

        $footerView = $this->view('app/views/Footer.php');
        $footerView->backButton = 'nope';

        if ($this->isAuthorized('TRAINEE')) {

            $reportView = $this->view('app/views/TraineeView.php');
            $reportView->reports = $this->service->findByTraineeId($this->sessionData('userId'));
            $reportView->viewHelper = $this->viewHelper;

        } elseif ($this->isAuthorized('TRAINER')) {

            $reportView = $this->view('app/views/TrainerView.php');
            $reportView->userService = $this->userService;
            $reportView->viewHelper = $this->viewHelper;
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

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($reportView->render());
        $this->response->addBody($footerView->render());
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
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($reportView->render());
            $this->response->addBody($footerView->render());
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
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($reportView->render());
            $this->response->addBody($footerView->render());
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
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($reportView->render());
            $this->response->addBody($footerView->render());
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

                foreach ($this->requestValidator->errorCodes() as $errorCode) {
                    $errorMessage[] = $this->getErrorMessageForErrorCode($errorCode);
                }

                $reportView->errorMessages = $errorMessage;
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
                $infobarView->viewHelper = $this->viewHelper;
                $infobarView->username = $this->sessionData('username');
                $infobarView->role = $this->sessionData('role');

                $footerView = $this->view('app/views/Footer.php');
                $footerView->backButton = 'show';

                $this->response->addBody($headerView->render());
                $this->response->addBody($infobarView->render());
                $this->response->addBody($reportView->render());
                $this->response->addBody($footerView->render());
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
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');

            $footerView = $this->view('app/views/Footer.php');
            $footerView->backButton = 'show';

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($reportView->render());
            $this->response->addBody($footerView->render());
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

    /**
     * @param int $errorCode
     */
    public function getErrorMessageForErrorCode(int $errorCode)
    {
        switch ($errorCode) {

            case Validator::ERR_VALIDATOR_DATE:
                return 'Der eingegebene Wert ist kein Datum!' . "\n";

            case Validator::ERR_VALIDATOR_INT:
                return 'Der eingegebene Wert ist keine Kalenderwoche!' . "\n";

        }
    }
}
