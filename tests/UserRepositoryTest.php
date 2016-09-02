<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

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
        $role = 'Trainee';

        $userRepository = new UserRepository();
        $createdUser = $userRepository->createUser($forename, $surname, $email, $role);

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
        $role = 'Trainee';

        $userRepository = new UserRepository();

        $createdUser = $userRepository->createUser($forename, $surname, $email, $role);
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
        $role = 'Trainee';

        $userRepository = new UserRepository();

        $createdUser = $userRepository->createUser($forename, $surname, $email, $role);

        $foundUser = $userRepository->findUserbyEmail($email);

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
        $role = 'Trainee';

        $userRepository = new UserRepository();

        $createdUser = $userRepository->createUser($forename, $surname, $email, $role);

        $foundUser = $userRepository->findUserbySurname($surname);

        $this->assertEquals($surname, $foundUser->surname());
    }
}
