<?php

namespace Jimdo\Reports\Profile;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\MongoSerializer;

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

    protected function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');

        $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
            , $appConfig->mongoUsername
            , $appConfig->mongoPassword
            , $appConfig->mongoHost
            , $appConfig->mongoPort
            , $appConfig->mongoDatabase
        );

        $this->client = new \MongoDB\Client($uri);

        $reportbook = $this->client->selectDatabase($appConfig->mongoDatabase);

        $this->profiles = $reportbook->profiles;

        $this->profiles->deleteMany([]);

        $this->repository = new ProfileMongoRepository($this->client, new MongoSerializer(), $appConfig);
        $this->service = new ProfileService($this->repository, $appConfig->defaultProfile);
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

    /**
     * @test
     */
    public function itShouldFindProfileByUserId()
    {
        $userId = uniqid();
        $forename = 'Tom';
        $surname = 'TomTom';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $foundProfile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($profile->forename(), $foundProfile->forename());
    }

    /**
     * @test
     */
    public function itShouldEditForename()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newForename = 'jennypenny';
        $this->service->editForename($userId, $newForename);

        $foundProfile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newForename, $foundProfile->forename());
    }

    /**
     * @test
     */
    public function itShouldEditSurname()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newSurname = 'jennypenny';

        $this->service->editSurname($userId, $newSurname);

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newSurname, $profile->surname());
    }

    /**
     * @test
     */
    public function itShouldEditDateOfBirth()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newDateOfBirth = '11.11.11';

        $this->service->editDateOfBirth($userId, $newDateOfBirth);

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newDateOfBirth, $profile->dateOfBirth());
    }

    /**
     * @test
     */
    public function itShouldEditSchool()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newSchool = '11.11.11';

        $this->service->editSchool($userId, $newSchool);

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newSchool, $profile->school());
    }

    /**
     * @test
     */
    public function itShouldEditCompany()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newCompany = 'Jimdo';

        $this->service->editCompany($userId, $newCompany);

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newCompany, $profile->company());
    }

    /**
     * @test
     */
    public function itShouldEditJobTitle()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newJobTitle = '11.11.11';

        $this->service->editJobTitle($userId, $newJobTitle);

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newJobTitle, $profile->jobTitle());
    }

    /**
     * @test
     */
    public function itShouldEditStartOfTraining()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newStartOfTraining = '11.11.11';

        $this->service->editStartOfTraining($userId, $newStartOfTraining);

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newStartOfTraining, $profile->startOfTraining());
    }

    /**
     * @test
     */
    public function itShouldEditTrainingYear()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newTrainingYear = '1';

        $this->service->editTrainingYear($userId, $newTrainingYear);

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newTrainingYear, $profile->trainingYear());
    }

    /**
     * @test
     */
    public function itShouldEditGrade()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newGrade = '1fc';

        $this->service->editGrade($userId, $newGrade);

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newGrade, $profile->grade());
    }

    /**
     * @test
     */
    public function itShouldEditImage()
    {
        $userId = uniqid();
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->service->createProfile($userId, $forename, $surname);

        $newImage = '1fc';

        $this->service->editImage($userId, $newImage, 'png');

        $profile = $this->service->findProfileByUserId($userId);

        $this->assertEquals($newImage, $profile->image());
    }
}
