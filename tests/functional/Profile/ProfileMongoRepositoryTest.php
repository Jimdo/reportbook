<?php

namespace Jimdo\Reports\Profile;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Profile\Profile as Profile;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class ProfileMongoRepositoryTest extends TestCase
{
    /** @var Client */
    private $client;

    /** @var Collection */
    private $profiles;

    /** @var ApplicationConfig */
    private $appConfig;

    protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');

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
    }

    /**
     * @test
     */
    public function itShouldCreateProfile()
    {
        $repository = new ProfileMongoRepository($this->client, new Serializer(), $this->appConfig);

        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';
        $profile = $repository->createProfile($userId, $forename, $surname);

        $this->assertEquals($forename, $profile->forename());
    }

    /**
     * @test
     */
    public function itShouldFindProfileByUserId()
    {
        $repository = new ProfileMongoRepository($this->client, new Serializer(), $this->appConfig);

        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';
        $profile = $repository->createProfile($userId, $forename, $surname);

        $foundProfile = $repository->findProfileByUserId($userId);

        $this->assertEquals($profile->forename(), $foundProfile->forename());
    }

    /**
     * @test
     */
    public function itShouldCheckIfProfileExists()
    {
        $repository = new ProfileMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $userId = uniqid();

        $this->assertFalse($repository->exists($userId));

        $repository->createProfile($userId, $forename, $surname);

        $this->assertTrue($repository->exists($userId));
    }

    /**
     * @test
     */
    public function itShouldDeleteProfile()
    {
        $repository = new ProfileMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $userId = uniqid();

        $profile = $repository->createProfile($userId, $forename, $surname);

        $foundProfile = $repository->findProfileByUserId($userId);
        $this->assertEquals($profile->surname(), $foundProfile->surname());

        $repository->deleteProfile($profile);

        $foundProfile = $repository->findProfileByUserId($userId);
        $this->assertEquals(null, $foundProfile);
    }
}
