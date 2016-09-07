<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class UserFileRepositoryTest extends TestCase
{
    const USERS_ROOT_PATH = 'tests/users';

    protected function setUp()
    {
        $this->deleteRecursive(self::USERS_ROOT_PATH);
    }

    protected function tearDown()
    {
        $this->deleteRecursive(self::USERS_ROOT_PATH);
    }

    /**
     * @test
     */
    public function itShouldCreateUser()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $expectedUser = $repository->createUser($forename, $surname, $email, $role, '12345678910');

        $userFileName = sprintf('%s/%s'
            , self::USERS_ROOT_PATH
            , $expectedUser->id()
        );

        $user = unserialize(file_get_contents($userFileName));

        $this->assertEquals($expectedUser->id(), $user->id());
    }

    /**
    * @test
    */
    public function itShouldDeleteUser()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $user = $repository->createUser($forename, $surname, $email, $role, '12345678910');

        $userFileName = sprintf('%s/%s'
            , self::USERS_ROOT_PATH
            , $user->id()
        );

        $this->assertTrue(file_exists($userFileName));

        $repository->deleteUser($user);

        $this->assertFalse(file_exists($userFileName));
    }

    /**
    * @test
    */
    public function itShouldFindUserByEmail()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $expectedUser = $repository->createUser($forename, $surname, $email, $role, '12345678910');

        $user = $repository->findUserByEmail($email);

        $this->assertEquals($expectedUser->id(), $user->id());
    }

    /**
    * @test
    */
    public function itShouldFindAllUsers()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $forename = 'Max';
        $surname = 'Mustermann';
        $role = new Role('trainee');

        $expectedUser = $repository->createUser($forename, $surname, 'max.mustermann@hotmail.de', $role, '12345678910');
        $foundUsers = $repository->findAllUsers();

        $this->assertCount(1, $foundUsers);

        $expectedUser = $repository->createUser($forename, $surname, 'peter.mustermann@hotmail.de', $role, '12345678910');
        $foundUsers = $repository->findAllUsers();

        $this->assertCount(2, $foundUsers);
    }

    /**
    * @test
    */
    public function itShouldFindUserBySurname()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $expectedUser = $repository->createUser($forename, $surname, $email, $role, '12345678910');

        $user = $repository->findUserBySurname($surname);

        $this->assertEquals($expectedUser->surname(), $user->surname());
    }

    /**
    * @test
    */
    public function itShouldFindUserById()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $expectedUser = $repository->createUser($forename, $surname, $email, $role, '12345678910');

        $user = $repository->findUserById($expectedUser->id());

        $this->assertInternalType('string', $user->id());
    }


    private function deleteRecursive($input)
    {
        if (is_file($input)) {
            unlink($input);
            return;
        }

        if (is_dir($input)) {
            foreach (scandir($input) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $file = join('/', [$input, $file]);
                if (is_file($file)) {
                    unlink($file);
                    continue;
                }

                $this->deleteRecursive($file);

                rmdir($file);
            }
        }
    }
}
