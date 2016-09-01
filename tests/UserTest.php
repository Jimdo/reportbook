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
        $user = new User($forename, $surname, $email);

        $this->assertEquals($forename, $user->forename());
        $this->assertEquals($surname, $user->surname());
    }

    /**
    * @test
    */
    public function itShouldHaveUserEmailAddress()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $user = new User($forename, $surname, $email);

        $this->assertEquals($email, $user->email());
    }
}
