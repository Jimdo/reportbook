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
}