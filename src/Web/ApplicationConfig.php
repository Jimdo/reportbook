<?php

namespace Jimdo\Reports\Web;

class ApplicationConfig
{
    /** @var string */
    private $MONGO_SERVER_IP;

    /** @var string */
    private $uri;

    public function __construct()
    {
        $this->MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $this->uri = 'mongodb://' . $this->MONGO_SERVER_IP . ':27017';;
    }

    public function mongoUri()
    {
        return $this->uri;
    }
}
