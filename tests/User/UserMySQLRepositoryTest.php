<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Serializer;
use Jimdo\Reports\Web\ApplicationConfig;

class UserMySQLRepositoryTest extends TestCase
{
    /** @var PDO */
    private $dbHandler;

    /** @var ReportMySQLRepository */
    private $repository;

    /** @var MySQL Database */
    private $database;

    /** @var MySQL Table */
    private $table;

    /** @var Serializer */
    private $serializer;

    protected function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

        $this->database = $appConfig->mysqlDatabase;
        $this->table = 'user';

        $uri = "mysql:host={$appConfig->mysqlHost};dbname={$this->database}";

        $this->dbHandler = new \PDO($uri, $appConfig->mysqlUser, $appConfig->mysqlPassword);

        $this->serializer = new Serializer();
        $this->repository = new UserMySQLRepository($this->dbHandler, $this->serializer, $appConfig);

        $this->dbHandler->exec("DELETE FROM user");
    }

    /**
     * @test
     */
    public function itShouldCreateUser()
    {
        $username = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role(Role::TRAINER);
        $password = 'SecurePassword123';

        $user = $this->repository->createUser($username, $email, $role, $password);

        $query = $this->dbHandler->query("SELECT * FROM {$this->table} WHERE id = '{$user->id()}'");

        $foundUser = $this->serializer->unserializeUser($query->fetchAll()[0]);

        $this->assertEquals($user->id(), $foundUser->id());
    }
}
