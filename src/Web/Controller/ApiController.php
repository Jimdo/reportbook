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
        if ($this->isAuthorized()) {
            $user = $this->appService->findUserByUsername($this->queryParams('username'));

            $serializedUser = $this->serializer->serializeWebUser($user);

            $this->response->addHeader('Content-Type: application/json');
            $this->response->addBody($serializedUser);
            $this->response->render();
        } else {
            echo "Not authorized!\n";
        }
    }

    public function userByEmailAction()
    {
        if ($this->isAuthorized()) {
            $user = $this->appService->findUserByEmail($this->queryParams('email'));

            $serializedUser = $this->serializer->serializeWebUser($user);
          
            $this->response->addHeader('Content-Type: application/json');
            $this->response->addBody($serializedUser);
            $this->response->render();
        } else {
            echo "Not authorized!\n";
        }
    }

    public function userByIdAction()
    {
        if ($this->isAuthorized()) {
            $user = $this->appService->findUserById($this->queryParams('id'));

            $serializedUser = $this->serializer->serializeWebUser($user);

            $this->response->addHeader('Content-Type: application/json');
            $this->response->addBody($serializedUser);
            $this->response->render();
        } else {
            echo "Not authorized!\n";
        }
    }

    public function usersAction()
    {
        if ($this->isAuthorized()) {
            $users = array_merge($this->appService->findAllTrainees(), $this->appService->findAllTrainers());

            $userOutput = [];
            foreach ($users as $user) {
                $userOutput[] = json_decode($this->serializer->serializeWebUser($user), true);
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->addBody(json_encode($userOutput));
            $this->response->render();
        } else {
            echo "Not authorized!\n";
        }
    }
}
