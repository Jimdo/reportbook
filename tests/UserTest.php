<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
    * @test
    */
    public function itShouldHaveForeAndSurname()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = 'Trainee';
        $user = new User($forename, $surname, $email, $role);

        $this->assertEquals($forename, $user->forename());
        $this->assertEquals($surname, $user->surname());
    }

    /**
    * @test
    */
    public function itShouldHaveEmailAddress()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = 'Trainee';
        $user = new User($forename, $surname, $email, $role);

        $this->assertEquals($email, $user->email());
    }

    /**
    * @test
    */
    public function itShouldHaveRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = 'Trainee';
        $user = new User($forename, $surname, $email, $role);

        $this->assertEquals($role, $user->role());
    }

    /**
    * @test
    */
    public function itShouldEditUser()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = 'Trainee';
        $user = new User($forename, $surname, $email, $role);

        $this->assertEquals($forename, $user->forename());

        $forename = 'Peter';

        $user->edit($forename, $surname, $email, $role);

        $this->assertEquals($forename, $user->forename());

    }
}
