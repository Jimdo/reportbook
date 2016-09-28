<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

class StatusController extends Controller
{
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
        self::excludeFromSession('health');
    }

    public function healthAction()
    {
        header("Content-type: application/json");
        echo json_encode(['status' => 'ok']);
        http_response_code(200);
    }
}
