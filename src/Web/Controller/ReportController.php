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

    /** @var Twig_Environment */
    private $twig;

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

        $this->twig = $twig;

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
        if ($this->isTrainee()) {
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

            $template = $this->twig->load('Trainee.html');

        } elseif ($this->isTrainer()) {
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

            $template = $this->twig->load('Trainer.html');

        } elseif ($this->isAdmin()) {

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

            $template = $this->twig->load('Admin.html');

        } else {
            $this->redirect("/user");
        }

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'backButton' => false,
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'infoHeadline' => ' | Übersicht',
            'hideInfos' => false,
            'appService' => $this->appService,
            'reports' => $reports
        ];

        echo $template->render($variables);
    }

    public function calendarAction()
    {
        if (!$this->isTrainee() && !$this->isAdmin() && !$this->isTrainer()) {
            $this->redirect('/user');
        } else {
            $year = $this->queryParams('year');
            if ($year === null) {
                $year = date('Y');
            }
            if ($this->isAdmin() || $this->isTrainer()) {
                $users = $this->appService->findAllTrainees();
                foreach ($users as $user) {
                    $profile = $this->appService->findProfileByUserId($user->id());
                    $traineeInfo[] = ['name' => $profile->forename() . ' ' .  $profile->surname(), 'id' => $user->id()];
                }
            }

            $variables = [
                'tabTitle' => 'Berichtsheft',
                'backButton' => false,
                'viewHelper' => $this->viewHelper,
                'username' => $this->sessionData('username'),
                'role' => $this->sessionData('role'),
                'isTrainer' => $this->isTrainer(),
                'isAdmin' => $this->isAdmin(),
                'infoHeadline' => ' | Übersicht',
                'hideInfos' => false,
                'months' => ['Januar', 'Febuar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
                'year' => $year,
                'users' => $traineeInfo,
                'currentUserId' => $this->queryParams('userId'),
                'cwInfo' => $this->createCalendarArray($this->queryParams('userId'), $year)
            ];

            echo $this->twig->render('Calendar.html', $variables);
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
        $variables = [
            'tabTitle' => 'Berichtsheft',
            'backButton' => true,
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'infoHeadline' => ' | Übersicht',
            'hideInfos' => false,
            'action' => '/report/create',
            'legend' => 'Neuen Bericht erstellen',
            'calendarWeek' => date("W"),
            'calendarYear' => date("Y"),
            'content' => '',
            'buttonName' => 'Bericht erstellen',
            'reportId' => null,
            'isTrainee' => $this->isTrainee(),
            'createButton' => true,
            'statusButtons' => false,
            'isCompany' => 'checked',
            'showCreateCommentButton' => false
        ];

        echo $this->twig->render('Report.html', $variables);
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

            foreach ($this->requestValidator->errorCodes() as $errorCode) {
                $errorMessages[] = $this->getErrorMessageForErrorCode($errorCode);
            }
            $variables = [
                'tabTitle' => 'Berichtsheft',
                'backButton' => true,
                'viewHelper' => $this->viewHelper,
                'username' => $this->sessionData('username'),
                'role' => $this->sessionData('role'),
                'isTrainer' => $this->isTrainer(),
                'isAdmin' => $this->isAdmin(),
                'infoHeadline' => ' | Übersicht',
                'hideInfos' => false,
                'action' => '/report/create',
                'legend' => 'Neuen Bericht erstellen',
                'calendarWeek' => $this->formData('calendarWeek'),
                'calendarYear' => $this->formData('calendarYear'),
                'content' => $this->formData('content'),
                'buttonName' => 'Bericht erstellen',
                'reportId' => null,
                'isTrainee' => $this->isTrainee(),
                'createButton' => true,
                'statusButtons' => false,
                'isCompany' => 'checked',
                'errorMessages' => $errorMessages,
                'showCreateCommentButton' => false
            ];

            echo $this->twig->render('Report.html', $variables);
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

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'backButton' => true,
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'isAdmin' => $this->isAdmin(),
            'infoHeadline' => ' | Übersicht',
            'hideInfos' => false,
            'action' => '/report/edit',
            'legend' => 'Bericht bearbeiten',
            'calendarWeek' => $report->calendarWeek(),
            'calendarYear' => $report->calendarYear(),
            'content' => $report->content(),
            'buttonName' => 'Speichern',
            'reportId' => $reportId,
            'isTrainee' => $this->isTrainee(),
            'createButton' => true,
            'statusButtons' => false,
            'isCompany' => $isCompany,
            'isSchool' => $isSchool,
            'appService' => $this->appService,
            'comments' => $this->appService->findCommentsByReportId($reportId),
            'userId' => $this->sessionData('userId'),
            'traineeId' => $this->sessionData('userId'),
            'report' => $this->appService->findReportById($reportId, $this->sessionData('userId')),
            'showCreateCommentButton' => ($report->status() !== 'NEW' && $report->status() !== 'EDITED' && $report->status() !== 'APPROVED')
        ];

        echo $this->twig->render('Report.html', $variables);
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

            $isSchool = '';
            $isCompany = '';

            $report = $this->appService->findReportById($this->formData('reportId'), $this->sessionData('userId'), $this->isAdmin());

            if ($report->category() === Category::SCHOOL) {
                $isSchool = 'checked';
            } elseif ($report->category() === Category::COMPANY) {
                $isCompany = 'checked';
            }

            foreach ($this->requestValidator->errorCodes() as $errorCode) {
                $errorMessages[] = $this->getErrorMessageForErrorCode($errorCode);
            }

            $variables = [
                'tabTitle' => 'Berichtsheft',
                'backButton' => true,
                'viewHelper' => $this->viewHelper,
                'username' => $this->sessionData('username'),
                'role' => $this->sessionData('role'),
                'isTrainer' => $this->isTrainer(),
                'isAdmin' => $this->isAdmin(),
                'infoHeadline' => ' | Übersicht',
                'hideInfos' => false,
                'action' => '/report/edit',
                'legend' => 'Bericht bearbeiten',
                'calendarWeek' => $this->formData('calendarWeek'),
                'calendarYear' => $this->formData('calendarYear'),
                'content' => $this->formData('content'),
                'buttonName' => 'Speichern',
                'reportId' => $this->formData('reportId'),
                'isTrainee' => $this->isTrainee(),
                'createButton' => true,
                'statusButtons' => false,
                'isCompany' => $isCompany,
                'isSchool' => $isSchool,
                'appService' => $this->appService,
                'comments' => $this->appService->findCommentsByReportId($this->formData('reportId')),
                'userId' => $this->sessionData('userId'),
                'traineeId' => $this->sessionData('userId'),
                'report' => $this->appService->findReportById($this->formData('reportId'), $this->sessionData('userId')),
                'showCreateCommentButton' => ($report->status() !== 'NEW' && $report->status() !== 'EDITED' && $report->status() !== 'APPROVED'),
                'errorMessages' => $errorMessages
            ];

            echo $this->twig->render('Report.html', $variables);
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
                return 'Der eingegebene Wert ist keine Zahl!' . "\n";
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
