<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\User\Role as Role;

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

        $expectedUser = $repository->createUser($forename, $surname, 'Hans', $email, $role, '12345678910');

        $userFileName = sprintf('%s/%s', self::USERS_ROOT_PATH, $expectedUser->id());

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

        $user = $repository->createUser($forename, $surname, 'Hans', $email, $role, '12345678910');

        $userFileName = sprintf('%s/%s', self::USERS_ROOT_PATH, $user->id());

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

        $expectedUser = $repository->createUser($forename, $surname, 'Hans', $email, $role, '12345678910');

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

        $expectedUser = $repository->createUser($forename, $surname, 'Hase', 'max.mustermann@hotmail.de', $role, '12345678910');
        $foundUsers = $repository->findAllUsers();

        $this->assertCount(1, $foundUsers);

        $expectedUser = $repository->createUser($forename, $surname, 'Igel', 'peter.mustermann@hotmail.de', $role, '12345678910');
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

        $expectedUser = $repository->createUser($forename, $surname, 'Hans', $email, $role, '12345678910');

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

        $expectedUser = $repository->createUser($forename, $surname, 'Hans', $email, $role, '12345678910');

        $user = $repository->findUserById($expectedUser->id());

        $this->assertEquals($expectedUser->id(), $user->id());
    }

    /**
     * @test
     */
    public function itShouldFindUserByStatus()
    {
        $repository = new UserFileRepository(self::USERS_ROOT_PATH);

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $expectedUser1 = $repository->createUser($forename, $surname, 'Hans', $email, $role, '12345678910');
        $users = $repository->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(1, $users);

        $expectedUser2 = $repository->createUser($forename, $surname, 'Igel', 'maxi.mustermann@hotmail.de', $role, '12345678910');
        $users = $repository->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(2, $users);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\UserRepositoryException
     */
    public function itShouldThrowUserRepositoryExceptionOnDuplicateEmail()
    {
        $userRepository = new UserFileRepository(self::USERS_ROOT_PATH);

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $jenny = $userRepository->createUser('Max', 'Mustermann', 'Hase', 'max.mustermann@hotmail.de', $role, '12345678910');
        $tom = $userRepository->createUser('Max', 'Mustermann', 'Igel', 'max.mustermann@hotmail.de', $role, '12345678910');
    }

    /**
     * @test
     */
    public function itShouldCheckIfUserExistsByEmailOrUsername()
    {
        $userRepository = new UserFileRepository(self::USERS_ROOT_PATH);

        $username = 'hase2000';
        $mail = 'hase@123.org';

        $this->assertFalse($userRepository->exists($username));
        $this->assertFalse($userRepository->exists($mail));

        $userRepository->createUser('Hans', 'Hase', $username, $mail, new Role('trainer'), 'some password');

        $this->assertTrue($userRepository->exists($username));
        $this->assertTrue($userRepository->exists($mail));
    }

    /**
     * @test
     */
     public function itShouldHaveUniqId()
     {
         $userRepository = new UserFileRepository(self::USERS_ROOT_PATH);

         $forename = 'Max';
         $surname = 'Mustermann';
         $username = 'maxi';
         $email = 'max.mustermann@hotmail.de';
         $role = new Role('trainee');
         $password = '1234567';

         $user = $userRepository->createUser($forename, $surname, $username, $email, $role, $password);

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
