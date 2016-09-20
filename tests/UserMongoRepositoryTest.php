<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Role as Role;


class UserMongoRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateUser()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);
        $reportBook = $client->reportBook;
        $users = $reportBook->users;

        $repository = new UserMongoRepository($client, new Serializer());

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $serializedUser = $users->findOne(['username' => $username]);
        $unserializedUser = $repository->serializer->unserializeUser($serializedUser->getArrayCopy());

        $this->assertEquals($user->username(), $unserializedUser->username());

        $repository->deleteUser($user);
    }

    /**
     * @test
     */
    public function itShouldFindUserByEmail()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);

        $repository = new UserMongoRepository($client, new Serializer());

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserByEmail($email);

        $this->assertEquals($user->email(), $foundUser->email());

        $repository->deleteUser($user);
    }

    /**
     * @test
     */
    public function itShouldFindUserBySurname()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);

        $repository = new UserMongoRepository($client, new Serializer());

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserBySurname($surname);

        $this->assertEquals($user->surname(), $foundUser->surname());

        $repository->deleteUser($user);
    }

    /**
     * @test
     */
    public function itShouldFindUserById()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);

        $repository = new UserMongoRepository($client, new Serializer());

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserById($user->id());

        $this->assertEquals($user->id(), $foundUser->id());

        $repository->deleteUser($user);
    }

    /**
     * @test
     */
    public function itShouldFindUserByUsername()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);

        $repository = new UserMongoRepository($client, new Serializer());

        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'maxipro';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1234567';

        $user = $repository->createUser($forename, $surname, $username, $email, $role, $password);

        $foundUser = $repository->findUserByUsername($username);

        $this->assertEquals($user->username(), $foundUser->username());

        $repository->deleteUser($user);
    }

    /**
     * @test
     */
    public function itShouldFindAllUsers()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);

        $repository = new UserMongoRepository($client, new Serializer());

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

        $repository->deleteUser($user1);
        $repository->deleteUser($user2);
    }

    /**
     * @test
     */
    public function itShouldDeleteUser()
    {
        $MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
        $uri = 'mongodb://' . $MONGO_SERVER_IP . ':27017';
        $client = new \MongoDB\Client($uri);

        $repository = new UserMongoRepository($client, new Serializer());

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
}
