<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class ReportMongoRepositoryBench
{
    /** @var Client $client */
    private $client;

    /** @var Collection $users */
    private $reports;

    /** @var ApplicationConfig */
    private $appConfig;

    protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $this->appConfig->mongoUsername
            , $this->appConfig->mongoPassword
            , $this->appConfig->mongoHost
            , $this->appConfig->mongoPort
            , $this->appConfig->mongoDatabase
        );

        $this->client = new \MongoDB\Client($uri);

        $reportbook = $this->client->selectDatabase($this->appConfig->mongoDatabase);
        $this->reports = $reportbook->reports;


    }

    /**
     * @Revs(10)
     */
    public function benchFindAll()
    {
        $this->setUp();
        $this->reports->find();
    }
}
