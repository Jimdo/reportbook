<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    /**
     * @test
     */
    public function itShoultHaveRoleName()
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
    public function itShouldHaveRoleStatus()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $user = new User($forename, $surname, $email, $role, '12345678910');

        $this->assertEquals(Role::STATUS_NOT_APPROVED, $user->role()->status());
    }
}
