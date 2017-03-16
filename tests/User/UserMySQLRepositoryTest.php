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

    /**
     * @test
     */
    public function itShouldFindUserById()
    {
        $username = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role(Role::TRAINER);
        $password = 'SecurePassword123';

        $user = $this->repository->createUser($username, $email, $role, $password);

        $foundUser = $this->repository->findUserById($user->id());

        $this->assertEquals($user->id(), $foundUser->id());
    }

    /**
     * @test
     */
    public function itShouldFindAllUsers()
    {
        $role = new Role(Role::TRAINER);
        $password = 'SecurePassword123';

        $this->repository->createUser('karl', 'karl@me.com', $role, $password);
        $this->repository->createUser('otto', 'otto@me.com', $role, $password);
        $this->repository->createUser('klaus', 'klaus@me.com', $role, $password);

        $foundUsers = $this->repository->findAllUsers();

        $this->assertCount(3, $foundUsers);
    }

    /**
     * @test
     */
    public function itShouldFindUserByEmail()
    {
        $role = new Role(Role::TRAINER);
        $password = 'SecurePassword123';

        $correctUser = $this->repository->createUser('karl', 'karl@me.com', $role, $password);
        $wrongUser = $this->repository->createUser('otto', 'otto@me.com', $role, $password);

        $foundUser = $this->repository->findUserByEmail($correctUser->email());

        $this->assertNotEquals($foundUser->email(), $wrongUser->email());
        $this->assertEquals($foundUser->email(), $correctUser->email());
    }

    /**
     * @test
     */
    public function itShouldDeleteUser()
    {
        $username = 'hase';
        $email = 'hase@hotmail.de';
        $role = new Role(Role::TRAINEE);
        $password = 'SecurePassword123';

        $user = $this->repository->createUser($username, $email, $role, $password);

        $foundUser = $this->repository->findUserById($user->id());
        $this->assertEquals($user->id(), $foundUser->id());

        $this->repository->deleteUser($user);

        $foundUser = $this->repository->findUserById($user->id());
        $this->assertEquals(null, $foundUser);
    }

    /**
     * @test
     */
    public function itShouldFindUserByUsername()
    {
        $role = new Role(Role::TRAINER);
        $password = 'SecurePassword123';

        $correctUser = $this->repository->createUser('karl', 'karl@me.com', $role, $password);
        $wrongUser = $this->repository->createUser('otto', 'otto@me.com', $role, $password);

        $foundUser = $this->repository->findUserByUsername($correctUser->username());

        $this->assertNotEquals($foundUser->username(), $wrongUser->username());
        $this->assertEquals($foundUser->username(), $correctUser->username());
    }

    /**
     * @test
     */
    public function itShouldFindUsersByStatus()
    {
        $role = new Role(Role::TRAINER);
        $password = 'SecurePassword123';

        $user = $this->repository->createUser('karl', 'karl@me.com', $role, $password);

        $foundUser = $this->repository->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertEquals(Role::STATUS_NOT_APPROVED, $foundUser[0]->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldCheckIfUserExists()
    {
        $username = 'hase';
        $email = 'igel@hase.com';
        $role = new Role(Role::TRAINER);
        $password = 'SecurePassword123';

        $this->assertFalse($this->repository->exists($username));
        $this->assertFalse($this->repository->exists($email));

        $user = $this->repository->createUser($username, $email, $role, $password);

        $this->assertTrue($this->repository->exists($username));
        $this->assertTrue($this->repository->exists($email));
    }
}
