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
        $user = new User($forename, $surname);

        $this->assertEquals($forename, $user->forename());
        $this->assertEquals($surname, $user->surname());
    }
}
