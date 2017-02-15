<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Reportbook\Category;
use Jimdo\Reports\Reportbook\Report;
use Jimdo\Reports\Reportbook\TraineeId;

use Jimdo\Reports\Web\View;
use Jimdo\Reports\Web\ViewHelper;
use Jimdo\Reports\Web\RequestValidator;
use Jimdo\Reports\Web\Response;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Web\Request;
use Jimdo\Reports\Web\Validator\Validator;

use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;
use Jimdo\Reports\Notification\MailgunSubscriber;

use Jimdo\Reports\Application\ApplicationService;

use Jimdo\Reports\Serializer;

class ReportController extends Controller
{
    /** @var ViewHelper */
    private $viewHelper;

    /** @var ApplicationService */
    private $appService;

    /**
     * @param Request $request
     * @param RequestValidator $requestValidator
     * @param ApplicationConfig $appConfig
     * @param Response $response
     * @param Twig_Environment $twig
     */
    public function __construct(
        Request $request,
        RequestValidator $requestValidator,
        ApplicationConfig $appConfig,
        Response $response,
        \Twig_Environment $twig
    ) {
        parent::__construct($request, $requestValidator, $appConfig, $response, $twig);

        $notificationService = new NotificationService();

        $this->viewHelper = new ViewHelper();
        $this->appService = ApplicationService::create($appConfig, $notificationService);

        $eventTypes = [
            'approvalRequested',
            'reportApproved',
            'reportCreated',
            'reportDeleted',
            'reportEdited',
            'reportDisapproved'
        ];

        $emailEventTypes = [
            'reportCreated',
            'approvalRequested',
            'reportApproved',
            'reportDisapproved'
        ];

        $notificationService->register(new PapertrailSubscriber($eventTypes, $appConfig));
        $notificationService->register(new MailgunSubscriber($emailEventTypes, $appConfig));
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
            $reportView->viewHelper = $this->viewHelper;
            $reportView->commentService = $this->appService;
            $reports = $this->appService->findReportsByTraineeId($this->sessionData('userId'));

            switch ($this->queryParams('sort')) {
                case 'name':
                    $this->appService->sortArrayDescending('traineeId', $reports);
                    break;
                case 'calendarWeek':
                    $reports = $this->appService->sortReportsByCalendarWeekAndYear($reports);
                    break;
                case 'comment':
                    $this->appService->sortReportsByAmountOfComments($reports);
                    break;
                case 'category':
                    $this->appService->sortArrayDescending('category', $reports);
                    break;
                case 'status':
                    $this->appService->sortReportsByStatus(
                       [
                            Report::STATUS_DISAPPROVED,
                            Report::STATUS_REVISED,
                            Report::STATUS_NEW,
                            Report::STATUS_EDITED,
                            Report::STATUS_APPROVAL_REQUESTED,
                            Report::STATUS_APPROVED
                        ],
                        $reports
                    );
                    break;
            }
            $reportView->reports = $reports;
        } elseif ($this->isTrainer()) {
            $reportView = $this->view('src/Web/Controller/Views/TrainerView.php');
            $reportView->userService = $this->appService;
            $reportView->profileService = $this->appService;
            $reportView->viewHelper = $this->viewHelper;

            $reportView->commentService = $this->appService;

            $reports = array_merge(
                $this->appService->findReportsByStatus(Report::STATUS_APPROVAL_REQUESTED),
                $this->appService->findReportsByStatus(Report::STATUS_APPROVED),
                $this->appService->findReportsByStatus(Report::STATUS_DISAPPROVED),
                $this->appService->findReportsByStatus(Report::STATUS_REVISED)
            );
            switch ($this->queryParams('sort')) {
                case 'name':
                    $this->appService->sortArrayDescending('traineeId', $reports);
                    break;
                case 'calendarWeek':
                    $reports = $this->appService->sortReportsByCalendarWeekAndYear($reports);
                    break;
                case 'comment':
                    $this->appService->sortReportsByAmountOfComments($reports);
                    break;
                case 'category':
                    $this->appService->sortArrayDescending('category', $reports);
                    break;
                case 'status':
                    $this->appService->sortReportsByStatus(
                        [
                            Report::STATUS_APPROVAL_REQUESTED,
                            Report::STATUS_REVISED,
                            Report::STATUS_DISAPPROVED,
                            Report::STATUS_APPROVED,
                            Report::STATUS_EDITED,
                            Report::STATUS_NEW
                        ],
                        $reports
                    );
                    break;
            }
            $reportView->reports = $reports;

            $infobarView->username = $this->sessionData('username');
            $infobarView->role = $this->sessionData('role');
            $infobarView->trainerRole = $this->isTrainer();
        } elseif ($this->isAdmin()) {
            $reportView = $this->view('src/Web/Controller/Views/AdminView.php');
            $reportView->userService = $this->appService;
            $reportView->profileService = $this->appService;
            $reportView->viewHelper = $this->viewHelper;
            $reportView->commentService = $this->appService;

            $reports = $this->appService->findAllReports();

            switch ($this->queryParams('sort')) {
                case 'name':
                    $this->appService->sortArrayDescending('traineeId', $reports);
                    break;
                case 'calendarWeek':
                    $reports = $this->appService->sortReportsByCalendarWeekAndYear($reports);
                    break;
                case 'comment':
                    $this->appService->sortReportsByAmountOfComments($reports);
                    break;
                case 'category':
                    $this->appService->sortArrayDescending('category', $reports);
                    break;
                case 'status':
                    $this->appService->sortReportsByStatus(
                        [
                            Report::STATUS_APPROVAL_REQUESTED,
                            Report::STATUS_REVISED,
                            Report::STATUS_DISAPPROVED,
                            Report::STATUS_APPROVED,
                            Report::STATUS_EDITED,
                            Report::STATUS_NEW
                        ],
                        $reports
                    );
                    break;
            }
            $reportView->reports = $reports;

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

    public function calendarAction()
    {
        if (!$this->isTrainee() && !$this->isAdmin() && !$this->isTrainer()) {
            $this->redirect('/user');
        } else {
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

            $calendarView = $this->view('src/Web/Controller/Views/CalendarView.php');
            $calendarView->viewHelper = $this->viewHelper;
            $calendarView->trainerRole = $this->isTrainer();
            $calendarView->adminRole = $this->isAdmin();
            $calendarView->months = ['Januar', 'Febuar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];

            $year = $this->queryParams('year');
            if ($year === null) {
                $year = date('Y');
            }
            $calendarView->year = $year;

            if ($this->isAdmin() || $this->isTrainer()) {
                $users = $this->appService->findAllTrainees();

                foreach ($users as $user) {
                    $profile = $this->appService->findProfileByUserId($user->id());
                    $traineeInfo[] = ['name' => $profile->forename() . ' ' .  $profile->surname(), 'id' => $user->id()];
                }
                $calendarView->users = $traineeInfo;
                $calendarView->currentUserId = $this->queryParams('userId');
                $calendarView->cwInfo = $this->createCalendarArray($this->queryParams('userId'), $year);
            } elseif ($this->isTrainee()) {
                $user = $this->queryParams('userId');
                $calendarView->currentUserId = $user;
                $calendarView->cwInfo = $this->createCalendarArray($user, $year);
            }

            $footerView = $this->view('src/Web/Controller/Views/Footer.php');
            $footerView->backButton = false;

            $this->response->addBody($headerView->render());
            $this->response->addBody($infobarView->render());
            $this->response->addBody($calendarView->render());
            $this->response->addBody($footerView->render());
        }
    }

    public function yearLaterAction()
    {
        if (!$this->isTrainee() && !$this->isAdmin() && !$this->isTrainer()) {
            $this->redirect('/user');
        } else {
            $year = intVal($this->queryParams('year'));
            if ($year <= date('Y')) {
                $year += 1;
            }
            $userId = $this->queryParams('userId');
            $this->redirect("/report/calendar", ['userId' => $userId, 'year' => $year]);
        }
    }

    public function yearBeforeAction()
    {
        if (!$this->isTrainee() && !$this->isAdmin() && !$this->isTrainer()) {
            $this->redirect('/user');
        } else {
            $year = intVal($this->queryParams('year')) - 1;
            $userId = $this->queryParams('userId');
            $this->redirect("/report/calendar", ['userId' => $userId, 'year' => $year]);
        }
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
            $reportView->calendarYear = date('Y');
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
        $this->addRequestValidation('calendarWeek', 'integer');
        $this->addRequestValidation('calendarYear', 'integer');
        $this->addRequestValidation('category', 'string');

        if ($this->isRequestValid()) {
            $this->appService->createReport(
                new TraineeId($this->sessionData('userId')),
                $this->formData('content'),
                $this->formData('calendarWeek'),
                $this->formData('calendarYear'),
                $this->formData('category')
            );
            $this->redirect("/report/list");
        } else {
            $reportView = $this->view('src/Web/Controller/Views/Report.php');
            $reportView->errorMessages = $this->requestValidator->errorMessages();
            $reportView->action = '/report/create';
            $reportView->legend = 'Neuen Bericht erstellen';
            $reportView->calendarWeek = $this->formData('calendarWeek');
            $reportView->calendarYear = $this->formData('calendarYear');
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
        $report = $this->appService->findReportById($reportId, $this->sessionData('userId'), $this->isAdmin());

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
        $reportView->calendarYear = $report->calendarYear();
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
        $commentsView->commentService = $this->appService;
        $commentsView->comments = $this->appService->findCommentsByReportId($reportId);
        $commentsView->userId = $this->sessionData('userId');
        $commentsView->reportId = $reportId;
        $commentsView->traineeId = $this->sessionData('userId');
        $commentsView->report = $this->appService->findReportById($reportId, $this->sessionData('userId'));
        $commentsView->userService = $this->appService;
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
        $this->addRequestValidation('calendarWeek', 'integer');
        $this->addRequestValidation('calendarYear', 'integer');
        $this->addRequestValidation('category', 'string');

        if ($this->isRequestValid()) {
            $this->appService->editReport(
                $this->formData('reportId'),
                $this->formData('content'),
                $this->formData('calendarWeek'),
                $this->formData('calendarYear'),
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
            $reportView->calendarYear = $this->formData('calendarYear');
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
        if ($this->isTrainee() && $this->appService
            ->findReportById($this->formData('reportId'), $this->sessionData('userId'))
            ->status() !== Report::STATUS_DISAPPROVED ||
            $this->isAdmin()
        ) {
                $this->appService->deleteReport($this->formData('reportId'));
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
            $this->appService->requestApproval($this->formData('reportId'));
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

        $report = $this->appService->findReportById($reportId, $traineeId);

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
        $reportView->calendarYear = $report->calendarYear();
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

        if (!$this->isTrainee()) {
            $user = $this->appService->findProfileByUserId($traineeId);
            $reportView->traineeName = 'von ' . $user->forename() . ' ' . $user->surname();
        }

        $commentsView = $this->view('src/Web/Controller/Views/CommentsView.php');
        $commentsView->commentService = $this->appService;
        $commentsView->comments = $this->appService->findCommentsByReportId($reportId);
        $commentsView->userId = $this->sessionData('userId');
        $commentsView->reportId = $reportId;
        $commentsView->traineeId = $traineeId;
        $commentsView->report = $this->appService->findReportById($reportId, $traineeId);
        $commentsView->userService = $this->appService;
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
            $this->appService->approveReport($this->formData('reportId'));
            $this->redirect("/report/list");
        }
    }

    public function disapproveReportAction()
    {
        if (!$this->isTrainer() && !$this->isAdmin()) {
            $this->redirect("/user");
        } else {
            $this->appService->disapproveReport($this->formData('reportId'));
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
            $reportView->reports = $this->appService->findReportsByString($this->formData('text'), $this->sessionData('userId'), $this->sessionData('role'));
            $reportView->viewHelper = $this->viewHelper;
            $reportView->commentService = $this->appService;
        } elseif ($this->isTrainer()) {
            $reportView = $this->view('src/Web/Controller/Views/TrainerView.php');
            $reportView->userService = $this->appService;
            $reportView->profileService = $this->appService;
            $reportView->viewHelper = $this->viewHelper;
            $reportView->reports = $this->appService->findReportsByString($this->formData('text'), $this->sessionData('userId'), $this->sessionData('role'));
            $reportView->commentService = $this->appService;
        } elseif ($this->isAdmin()) {
            $reportView = $this->view('src/Web/Controller/Views/AdminView.php');
            $reportView->userService = $this->appService;
            $reportView->profileService = $this->appService;
            $reportView->viewHelper = $this->viewHelper;
            $reportView->reports = $this->appService->findReportsByString($this->formData('text'), $this->sessionData('userId'), $this->sessionData('role'));
            $reportView->commentService = $this->appService;
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

    /**
     * @param string $traineeId
     * @return array
     */
    private function createCalendarArray(string $traineeId, string $year): array
    {
        $reports = $this->appService->findReportsByTraineeId($traineeId);

        for ($i=1; $i < 53; $i++) {
            $arr[$i] = '';
        }

        $arr = [];

        foreach ($reports as $report) {
            if ($year === $report->calendarYear()) {
                $arr[intVal($report->calendarWeek())] = [
                    'status' => $report->status(),
                    'traineeId' => $traineeId,
                    'reportId' => $report->id()
                ];
            }
        }
        return $arr;
    }
}
