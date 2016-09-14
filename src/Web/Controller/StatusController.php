<?php

namespace Jimdo\Reports\Web\Controller;

class StatusController extends Controller
{
    public function healthAction()
    {
        header("Content-type: application/json");
        echo json_encode(['status' => 'ok']);
        http_response_code(200);
    }
}
