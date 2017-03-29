<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request;
use Jimdo\Reports\Web\RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Web\Response;
use Jimdo\Reports\Web\ViewHelper;
use Jimdo\Reports\Web\Validator\Validator;

use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Application\ApplicationService;

class PrinterController extends Controller
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
    }

    public function printAction()
    {
        if (!$this->isTrainee()) {
            $this->redirect('/user');
        }

        $profile = $this->appService->findProfileByUserId($this->sessionData('userId'));

        $errorMessages = [];
        $years = [];
        $formDisabled = '';
        if ($profile->jobTitle() === '' || $profile->company() === '' || $profile->startOfTraining() === '') {
            $errorMessages[] = "Bitte fülle Dein Profil zuerst vollständig aus";
            $formDisabled = 'disabled';
        } else {
            $time = strtotime($profile->startOfTraining());
            $startYear = date("Y", $time);
            $years = [
                $startYear + 1,
                $startYear + 2,
                $startYear + 3,
                $startYear + 4
            ];
        }

        $variables = [
            'tabTitle' => 'Berichtsheft',
            'viewHelper' => $this->viewHelper,
            'username' => $this->sessionData('username'),
            'layout' => $_COOKIE['LAYOUT'],
            'userId' => $this->sessionData('userId'),
            'role' => $this->sessionData('role'),
            'isTrainer' => $this->isTrainer(),
            'printViewActive' => true,
            'isAdmin' => $this->isAdmin(),
            'infoHeadline' => ' | Übersicht',
            'hideInfos' => false,
            'heading' => 'Berichte herunterladen',
            'errorMessages' => $errorMessages,
            'formDisabled' => $formDisabled,
            'years' => $years
        ];

        echo $this->twig->render('PrintView.html', $variables);
    }

    public function createPdfAction()
    {
        if (!$this->isTrainee()) {
            $this->redirect('/user');
        }

        $userId = $this->sessionData('userId');

        if ($this->formData('title') === '0') {
            $trainerTitle = 'Herr';
        } elseif ($this->formData('title') === '1') {
            $trainerTitle = 'Frau';
        }

        switch ($this->formData('download')) {
            case 'reportbook':
                $this->appService->printReportbook(
                        $userId,
                        $trainerTitle,
                        $this->formData('forename'),
                        $this->formData('surname'),
                        $this->formData('companyStreet'),
                        $this->formData('companyCity')
                );
                break;
            case 'cover':
                $this->appService->printCover(
                        $userId,
                        $trainerTitle,
                        $this->formData('forename'),
                        $this->formData('surname'),
                        $this->formData('companyStreet'),
                        $this->formData('companyCity')
                );
                break;
            case 'reports':
                $this->appService->printReports(
                        $userId,
                        $this->formData('startMonth'),
                        $this->formData('startYear'),
                        $this->formData('endMonth'),
                        $this->formData('endYear')
                );
                break;
        }
    }
}
