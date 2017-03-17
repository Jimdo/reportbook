<?php

namespace Jimdo\Reports\Profile;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Serializer;
use Jimdo\Reports\Web\ApplicationConfig;

class ProfileMySQLRepositoryTest extends TestCase
{
    /** @var PDO */
    private $dbHandler;

    /** @var ReportMySQLRepository */
    private $repository;

    /** @var MySQL Database */
    private $database;

    /** @var MySQL Table */
    private $table;

    /** @var userId */
    private $userId;

    /** @var Serializer */
    private $serializer;

    protected function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

        $this->database = $appConfig->mysqlDatabase;
        $this->table = 'profile';

        $uri = "mysql:host={$appConfig->mysqlHost};dbname={$this->database}";

        $this->dbHandler = new \PDO($uri, $appConfig->mysqlUser, $appConfig->mysqlPassword);

        $this->serializer = new Serializer();
        $this->repository = new ProfileMySQLRepository($this->dbHandler, $this->serializer, $appConfig);

        $this->userId = uniqId();
        $this->dbHandler->exec("INSERT INTO user (
            id, username, email, password, roleName, roleStatus
        ) VALUES (
            '{$this->userId}', 'testuser', 'testemail', 'geheim', 'TRAINEE', 'APPROVED'
        )");
    }

    protected function tearDown()
    {
        $this->dbHandler->exec("DELETE FROM profile");
        $this->dbHandler->exec("DELETE FROM user");
    }

    /**
     * @test
     */
    public function itShouldCreateProfile()
    {
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->repository->createProfile($this->userId, $forename, $surname);

        $foundProfile = $this->repository->findProfileByUserId($profile->userId());

        $this->assertEquals($foundProfile->userId(), $profile->userId());
    }

    /**
     * @test
     */
    public function itShouldCheckIfProfileExists()
    {
        $forename = 'Max';
        $surname = 'Mustermann';

        $this->assertFalse($this->repository->exists($this->userId));
        $profile = $this->repository->createProfile($this->userId, $forename, $surname);

        $this->assertTrue($this->repository->exists($this->userId));
    }

    /**
     * @test
     */
    public function itShouldDeleteProfile()
    {
        $forename = 'Max';
        $surname = 'Mustermann';

        $profile = $this->repository->createProfile($this->userId, $forename, $surname);

        $this->repository->deleteProfile($profile);

        $this->assertNull($this->repository->findProfileByUserId($profile->userId()));
    }
}
