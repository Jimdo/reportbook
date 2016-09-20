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
    }
}
