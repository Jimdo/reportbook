<?php

namespace Jimdo\Reports\Web\Controller;

use \Jimdo\Reports\TeachingContentsMongoRepository;

class TeachingContentsController extends Controller
{
    public function searchAction()
    {
        header("Content-type: application/json");

        $uri = 'mongodb://' . getenv('MONGO_SERVER_IP') . ':27017';

        $teachingContentsReopsitory = new TeachingContentsMongoRepository(
            new \MongoDB\Client($uri)
        );

        $query = $this->queryParams('search');

        echo json_encode($teachingContentsReopsitory->search($query));
        http_response_code(200);
    }
}
