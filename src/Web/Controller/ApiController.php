<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\ApplicationConfig;

use Jimdo\Reports\Notification\BrowserNotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;
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

    /** @var ApplicationConfig */
    private $applicationConfig;

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

        $this->applicationConfig = $appConfig;

        $this->viewHelper = new ViewHelper();
        $this->appService = ApplicationService::create($appConfig, $notificationService);

        $this->serializer = new Serializer();

        $eventTypes = [
            'userAuthorized'
        ];

        $notificationService->register(new PapertrailSubscriber($eventTypes, $appConfig));
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

    public function authAction()
    {
        $authorized = $this->appService->authUser($this->formData('identifier'), $this->formData('password'));

        $this->response->addHeader('Content-Type: application/json');

        if ($authorized) {
            $userId = $this->appService->findUserByUsername($this->formData('identifier'))->id();

            $this->response->addBody(json_encode([
                'authorized' => true,
                'userId' => $userId,
                ]
            ));
        } else {
            $this->response->addBody(json_encode(['authorized' => false]));
        }

        $this->response->render();
    }

    private function isAuthorized()
    {
        if ($_SERVER['HTTP_API_TOKEN'] === $this->applicationConfig->apiToken) {
            return true;
        }
        return false;
    }
}
