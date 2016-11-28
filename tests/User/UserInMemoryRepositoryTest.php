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

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password);

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

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password);
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

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password);

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

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password);

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

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($username, $email, $role, $password);

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

        $foundUsers = $userRepository->findAllUsers();
        $this->assertCount(0, $foundUsers);

        $user1 = $userRepository->createUser('Hans', $email1, $role, $password);
        $user2 = $userRepository->createUser($username, $email2, $role, $password);

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

        $jenny = $userRepository->createUser('Hase', $email, $role, $password);
        $tom = $userRepository->createUser('Igel', $email, $role, $password);
    }

   /**
    * @test
    * @expectedException Jimdo\Reports\User\UserRepositoryException
    */
   public function itShouldThrowUserRepositoryExceptionOnDuplicateUsername()
    {
        $userRepository = new UserInMemoryRepository();

        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '12345678920';

        $jenny = $userRepository->createUser('Hase', 'jenny@email.com', $role, $password);
        $tom = $userRepository->createUser('Hase', $email, $role, $password);
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

        $jenny = $userRepository->createUser('Hase', $email, $role, $password);
    }
}
