<?php

namespace Jimdo\Reports\Web;

class ApplicationConfig
{
    /** @var string */
    private $db;

    /** @var string */
    private $uri;

    public function __construct()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $MONGO_SERVER_PORT = 27017;

        $MONGO_USERNAME = getenv('MONGO_USERNAME');
        $MONGO_PASSWORD = getenv('MONGO_PASSWORD');
        $MONGO_DATABASE = getenv('MONGO_DATABASE');

        $this->db = $MONGO_DATABASE;

        $this->uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $MONGO_USERNAME
            , $MONGO_PASSWORD
            , $MONGO_SERVER_IP
            , $MONGO_SERVER_PORT
            , $MONGO_DATABASE
        );
    }

    public function mongoDb()
    {
        return $this->db;
    }

    public function mongoUri()
    {
        return $this->uri;
    }
}
