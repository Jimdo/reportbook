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
        $role = new Role('trainee');
        $user = new User($forename, $surname, $email, $role, '12345678910');

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
        $role = new Role('trainee');
        $user = new User($forename, $surname, $email, $role, '12345678910');

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
        $role = new Role('trainee');
        $user = new User($forename, $surname, $email, $role, '12345678910');

        $this->assertEquals('trainee', $user->role()->name());
    }

    /**
    * @test
    */
    public function itShouldHavePassword()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'strongpassword';
        $user = new User($forename, $surname, $email, $role, $password);

        $this->assertEquals($password, $user->password());
    }

    /**
    * @test
    */
    public function itShouldEditUser()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $user = new User($forename, $surname, $email, $role, '12345678910');

        $this->assertEquals($forename, $user->forename());

        $forename = 'Peter';

        $user->edit($forename, $surname, $email, $role, '12345678910');

        $this->assertEquals($forename, $user->forename());
    }
}
