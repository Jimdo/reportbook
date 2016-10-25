<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Profile\ProfileService as ProfileService;
use Jimdo\Reports\Profile\ProfileMongoRepository as ProfileMongoRepository;
use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class ProfileController extends Controller
{

    /** @var ProfileService */
    private $profileService;

    /**
     * @param Request $request
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

        $profileRepository = new ProfileMongoRepository($client, new Serializer(), $appConfig);
        $this->profileService = new ProfileService($profileRepository);
    }

    public function imageAction()
    {
        $profileId = $this->queryParams('id');
        $profile = $this->profileService->findProfileByUserId($profileId);
        $base64 = $profile->image();
        $imageType = $profile->imageType();
        $data = base64_decode($base64);

        header('Content-Type: image/' . $imageType);

        echo $data;
    }

}
