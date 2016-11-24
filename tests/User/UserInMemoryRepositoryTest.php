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
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '12345678910';
        $isHashedPassword = false;

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password, $isHashedPassword);

        $this->assertEquals($email, $createdUser->email());
    }

    /**
     * @test
     */
    public function itShouldDeleteUser()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '12345678910';
        $isHashedPassword = false;

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password, $isHashedPassword);
        $this->assertCount(1, $userRepository->users);

        $userRepository->deleteUser($createdUser);

        $this->assertCount(0, $userRepository->users);
    }

    /**
     * @test
     */
    public function itShouldFindUserByEmail()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '12345678910';
        $isHashedPassword = false;

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password, $isHashedPassword);

        $foundUser = $userRepository->findUserByEmail($email);

        $this->assertEquals($email, $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindUserById()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '12345678910';
        $isHashedPassword = false;

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password, $isHashedPassword);

        $foundUser = $userRepository->findUserById($createdUser->id());

        $this->assertEquals($email, $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindUserBySurname()
    {
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '12345678910';
        $isHashedPassword = false;

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password, $isHashedPassword);

        $foundUser = $userRepository->findUserById($createdUser->id());

        $this->assertEquals($email, $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindAllUsers()
    {
        $userRepository = new UserInMemoryRepository();

        $username = 'Hase';
        $email1 = 'max.mustermann@hotmail.de';
        $email2 = 'hauke.stange@live.de';
        $role = new Role('trainee');
        $password = '12345678910';
        $isHashedPassword = false;

        $foundUsers = $userRepository->findAllUsers();
        $this->assertCount(0, $foundUsers);

        $user1 = $userRepository->createUser($username, $email1, $role, $password, $isHashedPassword);
        $user2 = $userRepository->createUser($username, $email2, $role, $password, $isHashedPassword);

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
        $password = '12345678920';
        $isHashedPassword = false;

        $jenny = $userRepository->createUser('Hase', $email, $role, $password, $isHashedPassword);
        $tom = $userRepository->createUser('Igel', $email, $role, $password, $isHashedPassword);
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
        $isHashedPassword = false;

        $jenny = $userRepository->createUser('Hase', $email, $role, $password, $isHashedPassword);
    }
}
