<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Role as Role;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;


class UserMongoRepositoryTest extends TestCase
{
    /** @var Client */
    private $client;

    /** @var Collection */
    private $users;

    /** @var ApplicationConfig */
    private $appConfig;

    protected function setUp()
    {
        $this->appConfig = new ApplicationConfig(__DIR__ . '/fixtures/config.yml');
        $uri = 'mongodb://' . $this->appConfig->mongoIp . ':27017';
        $this->client = new \MongoDB\Client($uri);
        $reportbook = $this->client->reportbook;
        $this->users = $reportbook->users;

        $this->users->deleteMany([]);
    }

    /**
     * @test
     */
    public function itShouldCreateUser()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $serializedUser = $this->users->findOne(['username' => $username]);
        $unserializedUser = $repository->serializer->unserializeUser($serializedUser->getArrayCopy());

        $this->assertEquals($user->username(), $unserializedUser->username());
    }

    /**
     * @test
     */
    public function itShouldFindUserByEmail()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserByEmail($email);

        $this->assertEquals($user->email(), $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindUserBySurname()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserBySurname($surname);

        $this->assertEquals($user->surname(), $foundUser->surname());
    }

    /**
     * @test
     */
    public function itShouldFindUserById()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserById($user->id());

        $this->assertEquals($user->id(), $foundUser->id());
    }

    /**
     * @test
     */
    public function itShouldFindUserByUsername()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserByUsername($username);

        $this->assertEquals($user->username(), $foundUser->username());
    }

    /**
     * @test
     */
    public function itShouldFindUserByStatus()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $role = new Role('trainee');
        $password = '1234567';

        $user1 = $repository->createUser($forename, $surname, 'max', 'max.mustermann@hotmail.de', $role, $password);
        $user2 = $repository->createUser($forename, $surname, 'maxi', 'maxi.mustermann@hotmail.de', $role, $password);
        $user3 = $repository->createUser($forename, $surname, 'maximan', 'maximan.mustermann@hotmail.de', $role, $password);

        $foundUsers = $repository->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(3, $foundUsers);
    }

    /**
     * @test
     */
    public function itShouldFindAllUsers()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $username1 = 'maxipro';
        $email1 = 'max.mustermann@hotmail.de';
        $username2 = 'peterhans';
        $email2 = 'peter.hans@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user1 = $repository->createUser($forename, $surname, $username1, $email1, $role, $password);
        $user2 = $repository->createUser($forename, $surname, $username2, $email2, $role, $password);

        $foundUsers = $repository->findAllUsers();

        $this->assertCount(2, $foundUsers);
    }

    /**
     * @test
     */
    public function itShouldDeleteUser()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipropi';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserByEmail($email);
        $this->assertEquals($user->email(), $foundUser->email());

        $repository->deleteUser($user);

        $foundUser = $repository->findUserByEmail($email);
        $this->assertEquals(null, $foundUser);
    }

    /**
     * @test
     */
    public function itShouldCheckIfUserExists()
    {
        $repository = new UserMongoRepository($this->client, new Serializer(), $this->appConfig);

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipropi';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $this->assertFalse($repository->exists($username));
        $this->assertFalse($repository->exists($email));

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $this->assertTrue($repository->exists($username));
        $this->assertTrue($repository->exists($email));
    }
}
