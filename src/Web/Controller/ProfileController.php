<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request;
use Jimdo\Reports\Web\Response;
use Jimdo\Reports\Web\RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig;

use Jimdo\Reports\Notification\NotificationService;
use Jimdo\Reports\Notification\PapertrailSubscriber;

use Jimdo\Reports\Application\ApplicationService;

class ProfileController extends Controller
{
    /** @var ApplicationService */
    private $appService;

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

        $notificationService = new NotificationService();
        $notificationService->register(new PapertrailSubscriber([], $appConfig));

        $this->appService = ApplicationService::create($appConfig, $notificationService);
    }

    public function imageAction()
    {
        $profileId = $this->queryParams('id');
        $profile = $this->appService->findProfileByUserId($profileId);
        $base64 = $profile->image();
        $imageType = $profile->imageType();
        $data = base64_decode($base64);

        header('Content-Type: image/' . $imageType);

        echo $data;
    }
}
