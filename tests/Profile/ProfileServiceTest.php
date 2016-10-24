<?php

namespace Jimdo\Reports\Profile;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class ProfileServiceTest extends TestCase
{
    /** @var ProfileService */
    private $service;

    /** @var ProfileMongoRepository */
    private $repository;

    /** @var Client */
    private $client;

    /** @var Collection */
    private $profiles;

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

        $this->profiles = $reportbook->profiles;

        $this->profiles->deleteMany([]);

        $this->repository = new ProfileMongoRepository($this->client, new Serializer(), $this->appConfig);
        $this->service = new ProfileService($this->repository);
    }
    /**
     * @test
     */
    public function itShouldCreateProfile($value='')
    {
        $userId = uniqid();
        $forename = 'Tom';
        $surname = 'TomTom';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $this->assertEquals($forename, $profile->forename());
    }
}
