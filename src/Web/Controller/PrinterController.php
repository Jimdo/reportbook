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
            'heading' => 'Berichte herunterladen'
        ];

        echo $this->twig->render('PrintView.html', $variables);
    }
}
