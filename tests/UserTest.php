<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
    * @test
    */
    public function itShouldHaveUserConstruct()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'strongpassword';
        $user = new User($forename, $surname, $email, $role, $password);

        $this->assertEquals($forename, $user->forename());
        $this->assertEquals($surname, $user->surname());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertInternalType('string', $user->id());
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

    /**
    * @test
    */
    public function itShouldHaveRoleConstruct()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $roleName = 'trainee';
        $user = new User($forename, $surname, $email, new Role($roleName), '12345678910');

        $this->assertEquals($roleName, $user->roleName());

        $this->assertEquals(Role::STATUS_NOT_APPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldChangeStatusOfRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $user = new User($forename, $surname, $email, $role, '12345678910');

        $user->approve();
        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());

        $user->disapprove();
        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }
}
