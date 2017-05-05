<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\ApplicationConfig;

use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;
use Jimdo\Reports\Notification\MailgunSubscriber;
use Jimdo\Reports\Serializer;
use Jimdo\Reports\Web\Request;
use Jimdo\Reports\Web\RequestValidator;
use Jimdo\Reports\Web\Response;
use Jimdo\Reports\Web\ViewHelper;

use Jimdo\Reports\Application\ApplicationService;

class ApiController extends Controller
{
    /** @var ApplicationService */
    private $appService;

    /** @var WebSerializer */
    private $serializer;

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

        $this->serializer = new Serializer();
    }

    public function userByUsernameAction()
    {
        $user = $this->appService->findUserByUsername($this->queryParams('username'));

        $serializedUser = $this->serializer->serializeWebUser($user);

        echo $serializedUser;
    }

    public function userByEmailAction()
    {
        $user = $this->appService->findUserByEmail($this->queryParams('email'));

        $serializedUser = $this->serializer->serializeWebUser($user);

        echo $serializedUser;
    }

    public function userByIdAction()
    {
        $user = $this->appService->findUserById($this->queryParams('id'));

        $serializedUser = $this->serializer->serializeWebUser($user);

        echo $serializedUser;
    }

    public function usersAction()
    {
        $users = array_merge($this->appService->findAllTrainees(), $this->appService->findAllTrainers());

        $userOutput = [];
        foreach ($users as $user) {
            $userOutput[] = json_decode($this->serializer->serializeWebUser($user), true);
        }

        echo json_encode($userOutput);
    }
}