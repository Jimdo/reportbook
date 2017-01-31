<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Web\ViewHelper as ViewHelper;
use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\Reportbook\TraineeId as TraineeId;
use Jimdo\Reports\Reportbook\CommentMongoRepository as CommentMongoRepository;
use Jimdo\Reports\Reportbook\CommentService as CommentService;
use Jimdo\Reports\Reportbook\ReportMongoRepository as ReportMongoRepository;
use Jimdo\Reports\Reportbook\ReportbookService as ReportbookService;
use Jimdo\Reports\Reportbook\Category as Category;
use Jimdo\Reports\Profile\ProfileService as ProfileService;
use Jimdo\Reports\Profile\ProfileMongoRepository as ProfileMongoRepository;
use Jimdo\Reports\User\UserMongoRepository as UserMongoRepository;
use Jimdo\Reports\User\UserService as UserService;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\Validator\Validator as Validator;
use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;
use Jimdo\Reports\Notification\MailgunSubscriber;

class ReportController extends Controller
{
    /** @var ReportbookService */
    private $service;

    /** @var UserService */
    private $userService;

    /** @var ProfileService */
    private $profileService;

    /** @var ViewHelper */
    private $viewHelper;

    /**
     * @param Request $request
     * @param RequestValidator $requestValidator
     * @param ApplicationConfig $appConfig
     * @param Response $response
     */
    public function __construct(
        Request $request,
        RequestValidator $requestValidator,
        ApplicationConfig $appConfig,
        Response $response
    ) {
        parent::__construct($request, $requestValidator, $appConfig, $response);

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $this->appConfig->mongoUsername
            , $this->appConfig->mongoPassword
            , $this->appConfig->mongoHost
            , $this->appConfig->mongoPort
            , $this->appConfig->mongoDatabase
        );

        $client = new \MongoDB\Client($uri);

        $notificationService = new NotificationService();

        $reportRepository = new ReportMongoRepository($client, new Serializer(), $appConfig);
        $commentRepository = new CommentMongoRepository($client, new Serializer(), $appConfig);
        $this->service = new ReportbookService($reportRepository, new CommentService($commentRepository), $appConfig, $notificationService);

        $eventTypes = [
            'approvalRequested',
            'reportApproved',
            'reportCreated',
            'reportDeleted',
            'reportEdited',
            'reportDisapproved'
        ];

        $emailEventTypes =[
            'reportCreated',
            'approvalRequested',
            'reportApproved',
            'reportDisapproved'
        ];

        $notificationService->register(new PapertrailSubscriber($eventTypes, $appConfig));
        $notificationService->register(new MailgunSubscriber($emailEventTypes, $appConfig));

        $userRepository = new UserMongoRepository($client, new Serializer(), $appConfig);
        $this->userService = new UserService($userRepository, $appConfig, $notificationService);
        $this->viewHelper = new ViewHelper();

        $profileRepository = new ProfileMongoRepository($client, new Serializer(), $appConfig);
        $this->profileService = new ProfileService($profileRepository, $appConfig->defaultProfile, $appConfig, $notificationService);
    }

    public function indexAction()
    {
        $this->redirect("/report/list");
    }

    public function listAction()
    {
        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->infoHeadline = ' | Übersicht';
        $infobarView->hideInfos = false;

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');
        $footerView->backButton = false;

        if ($this->isTrainee()) {
            $reportView = $this->view('src/Web/Controller/Views/TraineeView.php');
            $reportView->reports = $this->service->findByTraineeId($this->sessionData('userId'));
            $reportView->viewHelper = $this->viewHelper;
            $reportView->commentService = $this->service;
        } elseif ($this->isTrainer()) {
            $reportView = $this->view('src/Web/Controller/Views/TrainerView.php');
            $reportView->userService = $this->userService;
            $reportView->profileService = $this->profileService;
            $reportView->viewHelper = $this->viewHelper;
            $reportView->reports = array_merge(
                $this->service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
                $this->service->findByStatus(Report::STATUS_APPROVED),
                $this->service->findByStatus(Report::STATUS_DISAPPROVED),
                $this->service->findByStatus(Report::STATUS_REVISED)
            );
            $reportView->commentService = $this->service;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
        } elseif ($this->isAdmin()){
            $reportView = $this->view('src/Web/Controller/Views/AdminView.php');
            $reportView->userService = $this->userService;
            $reportView->profileService = $this->profileService;
            $reportView->viewHelper = $this->viewHelper;
            $reportView->reports = $this->service->findAll();
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->adminRole = $this->isAdmin();
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
        if (!$this->isTrainee()) {
            $this->redirect("/user");
        }
            $traineeId = $this->sessionData('userId');

            $reportView = $this->view('src/Web/Controller/Views/Report.php');
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = date('W');
            $reportView->date = date('d.m.Y');
            $reportView->content = '';
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->reportId = null;
            $reportView->backButton = true;
            $reportView->isTrainee = $this->isTrainee();
            $reportView->createButton = true;
            $reportView->statusButtons = false;
            $reportView->isCompany = 'checked';

            $headerView = $this->view('src/Web/Controller/Views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
            $infobarView->adminRole = $this->isAdmin();
            $infobarView->hideInfos = false;

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');
            $footerView->backButton = true;

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($reportView->render());
            $this->response->addBody($footerView->render());
    }

    public function createAction()
    {
        if (!$this->isTrainee()) {
            $this->redirect("/user");
        }

        $this->addRequestValidation('content', 'string');
        $this->addRequestValidation('date', 'date');
        $this->addRequestValidation('calendarWeek', 'integer');
        $this->addRequestValidation('category', 'string');

        if ($this->isRequestValid()) {
            $this->service->createReport(
                new TraineeId($this->sessionData('userId')),
                $this->formData('content'),
                $this->formData('date'),
                $this->formData('calendarWeek'),
                $this->formData('category')
            );
            $this->redirect("/report/list");
        } else {
            $reportView = $this->view('src/Web/Controller/Views/Report.php');
            $reportView->errorMessages = $this->requestValidator->errorMessages();
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = $this->formData('calendarWeek');
            $reportView->date = $this->formData('date');
            $reportView->content = $this->formData('content');
            $reportView->buttonName = 'Bericht erstellen';
            $reportView->backButton = true;
            $reportView->isTrainee = $this->isTrainee();
            $reportView->createButton = true;
            $reportView->statusButtons = false;

            $headerView = $this->view('src/Web/Controller/Views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
            $infobarView->adminRole = $this->isAdmin();
            $infobarView->hideInfos = false;

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');
            $footerView->backButton = true;

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($reportView->render());
            $this->response->addBody($footerView->render());
        }
    }

    public function editReportAction()
    {
        if (!$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        }

        $reportId = $this->formData('reportId');
        $report = $this->service->findById($reportId, $this->sessionData('userId'), $this->isAdmin());

        $isSchool = '';
        $isCompany = '';

        if ($report->category() === Category::SCHOOL) {
            $isSchool = 'checked';
        } elseif ($report->category() === Category::COMPANY) {
            $isCompany = 'checked';
        }

        $reportView = $this->view('src/Web/Controller/Views/Report.php');
        $reportView->action = '/report/edit';
        $reportView->legend = 'Bericht bearbeiten';
        $reportView->calendarWeek = $report->calendarWeek();
        $reportView->date = $report->date();
        $reportView->content = $report->content();
        $reportView->buttonName = 'Speichern';
        $reportView->reportId = $reportId;
        $reportView->backButton = true;
        $reportView->isTrainee = $this->isTrainee();
        $reportView->isAdmin = $this->isAdmin();
        $reportView->createButton = true;
        $reportView->statusButtons = false;
        $reportView->isSchool = $isSchool;
        $reportView->isCompany = $isCompany;

        $commentsView = $this->view('src/Web/Controller/Views/CommentsView.php');
        $commentsView->commentService = $this->service;
        $commentsView->comments = $this->service->findCommentsByReportId($reportId);
        $commentsView->userId = $this->sessionData('userId');
        $commentsView->reportId = $reportId;
        $commentsView->traineeId = $this->sessionData('userId');
        $commentsView->report = $this->service->findById($reportId, $this->sessionData('userId'));
        $commentsView->userService = $this->userService;
        $commentsView->viewHelper = $this->viewHelper;
        $commentsView->showCreateCommentButton = ($report->status() !== 'NEW' && $report->status() !== 'EDITED' && $report->status() !== 'APPROVED');

        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->adminRole = $this->isAdmin();
        $infobarView->hideInfos = false;

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');
        $footerView->backButton = true;

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($reportView->render());
        $this->response->addBody($commentsView->render());
        $this->response->addBody($footerView->render());
    }

    public function editAction()
    {
        if (!$this->isTrainee()) {
            $this->redirect("/user");
        }

        $this->addRequestValidation('content', 'string');
        $this->addRequestValidation('date', 'date');
        $this->addRequestValidation('calendarWeek', 'integer');
        $this->addRequestValidation('category', 'string');

        if ($this->isRequestValid()) {
            $this->service->editReport(
                $this->formData('reportId'),
                $this->formData('content'),
                $this->formData('date'),
                $this->formData('calendarWeek'),
                $this->formData('category')
            );
            $this->redirect("/report/list");
        } else {
            $reportView = $this->view('src/Web/Controller/Views/Report.php');

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
            $reportView->backButton = true;
            $reportView->isTrainee = $this->isTrainee();
            $reportView->createButton = true;
            $reportView->statusButtons = false;

            $headerView = $this->view('src/Web/Controller/Views/Header.php');
            $headerView->tabTitle = 'Berichtsheft';

            $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
            $infobarView->viewHelper = $this->viewHelper;
            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
            $infobarView->adminRole = $this->isAdmin();
            $infobarView->hideInfos = false;

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');
            $footerView->backButton = true;

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($reportView->render());
            $this->response->addBody($footerView->render());
        }
    }

    public function deleteReportAction()
    {
        if ($this->isTrainee() && $this->service
            ->findById($this->formData('reportId'), $this->sessionData('userId'))
            ->status() !== Report::STATUS_DISAPPROVED ||
            $this->isAdmin()
        ) {
                $this->service->deleteReport($this->formData('reportId'));
                $this->redirect("/report/list");
        } else {
            $this->redirect("/user");
        }
    }

    public function requestApprovalAction()
    {
        if (!$this->isTrainee() && !$this->isAdmin()) {
            $this->redirect("/user");
        } else {
            $this->service->requestApproval($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function viewReportAction()
    {
        if (!$this->isTrainee() && !$this->isTrainer() && !$this->isAdmin()) {
            $this->redirect("/user");
        }

        if ($this->formData('reportId') !== null && $this->formData('traineeId') !== null) {
            $reportId = $this->formData('reportId');
            $traineeId = $this->formData('traineeId');
        } else {
            $reportId = $this->queryParams('reportId');
            $traineeId = $this->queryParams('traineeId');
        }

        $report = $this->service->findById($reportId, $traineeId);

        $isSchool = '';
        $isCompany = '';

        if ($report->category() === Category::SCHOOL) {
            $isSchool = 'checked';
        } elseif ($report->category() === Category::COMPANY) {
            $isCompany = 'checked';
        }

        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->adminRole = $this->isAdmin();
        $infobarView->hideInfos = false;

        $reportView = $this->view('src/Web/Controller/Views/Report.php');
        $reportView->title = 'Bericht';
        $reportView->legend = 'Vorschau';
        $reportView->calendarWeek = $report->calendarWeek();
        $reportView->date = $report->date();
        $reportView->content = $report->content();
        $reportView->buttonName = 'Speichern';
        $reportView->backButton = true;
        $reportView->readonly = 'readonly';
        $reportView->radioReadonly = 'disabled';
        $reportView->trainerRole = $this->isTrainer();
        $reportView->creatButton = false;
        $reportView->reportId = $reportId;
        $reportView->isTrainee = $this->isTrainee();
        $reportView->isSchool = $isSchool;
        $reportView->isCompany = $isCompany;
        $reportView->statusButtons = (
            $this->isTrainer()
            && $report->status() !== Report::STATUS_DISAPPROVED
            && $report->status() !== Report::STATUS_APPROVED
            && $report->status() !== Report::STATUS_REVISED
            || $this->isAdmin()
            && $report->status() === Report::STATUS_APPROVAL_REQUESTED
        );

        $commentsView = $this->view('src/Web/Controller/Views/CommentsView.php');
        $commentsView->commentService = $this->service;
        $commentsView->comments = $this->service->findCommentsByReportId($reportId);
        $commentsView->userId = $this->sessionData('userId');
        $commentsView->reportId = $reportId;
        $commentsView->traineeId = $traineeId;
        $commentsView->report = $this->service->findById($reportId, $traineeId);
        $commentsView->userService = $this->userService;
        $commentsView->showCreateCommentButton = ($report->status() !== 'NEW' && $report->status() !== 'EDITED' && $report->status() !== 'APPROVED');
        $commentsView->viewHelper = $this->viewHelper;

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');
        $footerView->backButton = true;

        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($reportView->render());
        $this->response->addBody($commentsView->render());
        $this->response->addBody($footerView->render());
    }

    public function approveReportAction()
    {
        if (!$this->isTrainer() && !$this->isAdmin()) {
            $this->redirect("/user");
        } else {
            $this->service->approveReport($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function disapproveReportAction()
    {
        if (!$this->isTrainer() && !$this->isAdmin()) {
            $this->redirect("/user");
        } else {
            $this->service->disapproveReport($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function searchAction()
    {
        $headerView = $this->view('src/Web/Controller/Views/Header.php');
        $headerView->tabTitle = 'Berichtsheft';

        $infobarView = $this->view('src/Web/Controller/Views/Infobar.php');
        $infobarView->viewHelper = $this->viewHelper;
        $infobarView->username = $this->sessionData('username');
        $infobarView->role = $this->sessionData('role');
        $infobarView->trainerRole = $this->isTrainer();
        $infobarView->adminRole = $this->isAdmin();
        $infobarView->infoHeadline = ' | Übersicht';
        $infobarView->hideInfos = false;

        $footerView = $this->view('src/Web/Controller/Views/Footer.php');
        $footerView->backButton = true;

        if ($this->isTrainee()) {
            $reportView = $this->view('src/Web/Controller/Views/TraineeView.php');
            $reportView->reports = $this->service->findReportsByString($this->formData('text'), $this->sessionData('userId'), $this->sessionData('role'));
            $reportView->viewHelper = $this->viewHelper;
        } elseif ($this->isTrainer()) {
            $reportView = $this->view('src/Web/Controller/Views/TrainerView.php');
            $reportView->userService = $this->userService;
            $reportView->profileService = $this->profileService;
            $reportView->viewHelper = $this->viewHelper;
            $reportView->reports = $this->service->findReportsByString($this->formData('text'), $this->sessionData('userId'), $this->sessionData('role'));
        } elseif ($this->isAdmin()) {
            $reportView = $this->view('src/Web/Controller/Views/AdminView.php');
            $reportView->userService = $this->userService;
            $reportView->profileService = $this->profileService;
            $reportView->viewHelper = $this->viewHelper;
            $reportView->reports = $this->service->findReportsByString($this->formData('text'), $this->sessionData('userId'), $this->sessionData('role'));
        } else {
            $this->redirect("/user");
        }
        $this->response->addBody($headerView->render());
        $this->response->addBody($infobarView->render());
        $this->response->addBody($reportView->render());
        $this->response->addBody($footerView->render());
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
