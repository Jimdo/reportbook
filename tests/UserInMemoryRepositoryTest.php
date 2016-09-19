<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Role as Role;

class UserRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateUser()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();
        $createdUser = $userRepository->createUser($forename, $surname, 'Hase', $email, $role, '12345678910');

        $this->assertEquals($forename, $createdUser->forename());
    }

    /**
     * @test
     */
    public function itShouldDeleteUser()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();

        $createdUser = $userRepository->createUser($forename, $surname, 'Hase', $email, $role, '12345678910');
        $this->assertCount(1, $userRepository->users);

        $userRepository->deleteUser($createdUser);

        $this->assertCount(0, $userRepository->users);
    }

    /**
     * @test
     */
    public function itShouldFindUserByEmail()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();

        $createdUser = $userRepository->createUser($forename, $surname, 'Hase', $email, $role, '12345678910');

        $foundUser = $userRepository->findUserByEmail($email);

        $this->assertEquals($email, $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindUserById()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();

        $createdUser = $userRepository->createUser($forename, $surname, 'Hase', $email, $role, '12345678910');

        $foundUser = $userRepository->findUserById($createdUser->id());

        $this->assertEquals($email, $foundUser->email());
    }

    /**
     * @test
     */
    public function itShouldFindUserBySurname()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $userRepository = new UserInMemoryRepository();

        $createdUser = $userRepository->createUser($forename, $surname, 'Hase', $email, $role, '12345678910');

        $foundUser = $userRepository->findUserBySurname($surname);

        $this->assertEquals($surname, $foundUser->surname());
    }

    /**
     * @test
     */
    public function itShouldFindAllUsers()
    {
        $userRepository = new UserInMemoryRepository();

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $foundUsers = $userRepository->findAllUsers();
        $this->assertCount(0, $foundUsers);

        $user1 = $userRepository->createUser('Max', 'Mustermann', 'Hase', 'max.mustermann@hotmail.de', $role, '12345678910');
        $user2 = $userRepository->createUser('Hauke', 'Stange', 'Igel', 'hauke.stange@live.de', $role, '12345678910');

        $foundUsers = $userRepository->findAllUsers();
        $this->assertCount(2, $foundUsers);
    }


   /**
    * @test
    * @expectedException Jimdo\Reports\UserRepositoryException
    */
   public function itShouldThrowUserRepositoryExceptionOnDuplicateEmail()
    {
        $userRepository = new UserInMemoryRepository();

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');

        $jenny = $userRepository->createUser('Max', 'Mustermann', 'Hase', 'max.mustermann@hotmail.de', $role, '12345678910');
        $tom = $userRepository->createUser('Max', 'Mustermann', 'Igel', 'max.mustermann@hotmail.de', $role, '12345678910');
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\UserRepositoryException
     */
    public function itShouldThrowExceptionWhenPasswordIsShorterThatSevenChars()
    {
        $userRepository = new UserInMemoryRepository();

        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '123456';

        $jenny = $userRepository->createUser('Max', 'Mustermann', 'Hase', 'max.mustermann@hotmail.de', $role, $password);
    }
}
