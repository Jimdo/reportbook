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
}
