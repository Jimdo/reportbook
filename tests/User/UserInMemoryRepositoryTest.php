<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\User\Role as Role;

class UserRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateUser()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser('Hase', $email, $role, '12345678910');

        $this->assertEquals($email, $createdUser->email());
    }

    /**
     * @test
     */
    public function itShouldDeleteUser()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();

        $createdUser = $userRepository->createUser('Hase', $email, $role, '12345678910');
        $this->assertCount(1, $userRepository->users);

        $userRepository->deleteUser($createdUser);

        $this->assertCount(0, $userRepository->users);
    }

    /**
     * @test
     */
    public function itShouldFindUserByEmail()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();

        $createdUser = $userRepository->createUser('Hase', $email, $role, '12345678910');

        $foundUser = $userRepository->findUserByEmail($email);

        $this->assertEquals($email, $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindUserById()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();

        $createdUser = $userRepository->createUser('Hase', $email, $role, '12345678910');

        $foundUser = $userRepository->findUserById($createdUser->id());

        $this->assertEquals($email, $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindUserBySurname()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();

        $createdUser = $userRepository->createUser('Hase', $email, $role, '12345678910');

        $foundUser = $userRepository->findUserById($createdUser->id());

        $this->assertEquals($email, $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindAllUsers()
    {
        $userRepository = new UserInMemoryRepository();

        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $foundUsers = $userRepository->findAllUsers();
        $this->assertCount(0, $foundUsers);

        $user1 = $userRepository->createUser('Hase', 'max.mustermann@hotmail.de', $role, '12345678910');
        $user2 = $userRepository->createUser('Igel', 'hauke.stange@live.de', $role, '12345678910');

        $foundUsers = $userRepository->findAllUsers();
        $this->assertCount(2, $foundUsers);
    }


   /**
    * @test
    * @expectedException Jimdo\Reports\User\UserRepositoryException
    */
   public function itShouldThrowUserRepositoryExceptionOnDuplicateEmail()
    {
        $userRepository = new UserInMemoryRepository();

        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $jenny = $userRepository->createUser('Hase', 'max.mustermann@hotmail.de', $role, '12345678910');
        $tom = $userRepository->createUser('Igel', 'max.mustermann@hotmail.de', $role, '12345678910');
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\UserRepositoryException
     */
    public function itShouldThrowExceptionWhenPasswordIsShorterThatSevenChars()
    {
        $userRepository = new UserInMemoryRepository();

        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '123456';

        $jenny = $userRepository->createUser('Hase', 'max.mustermann@hotmail.de', $role, $password);
    }
}
