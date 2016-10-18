<?php

namespace Jimdo\Reports\User;

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
        $user = new User($forename, $surname, 'Hase', $email, $role, $password, new UserId());

        $this->assertEquals($forename, $user->forename());
        $this->assertEquals($surname, $user->surname());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertInternalType('string', $user->id());
        $this->assertEquals('', $user->dateOfBirth());
        $this->assertEquals('', $user->school());
        $this->assertEquals('', $user->grade());
        $this->assertEquals('', $user->jobTitle());
        $this->assertEquals('', $user->trainingYear());
        $this->assertEquals('', $user->company());
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
        $user = new User($forename, $surname, 'Hase', $email, $role, '12345678910', new UserId());

        $this->assertEquals($forename, $user->forename());

        $forename = 'Peter';

        $user->edit($forename, $surname, 'Hase', $email, '12345678910');

        $this->assertEquals($forename, $user->forename());
    }

    /**
    * @test
    */
    public function itShouldEditPassword()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $oldPassword = '1111111';
        $user = new User($forename, $surname, 'Hase', $email, $role, $oldPassword, new UserId());

        $newPassword = 'peterlustig';

        $user->editPassword($oldPassword, $newPassword);

        $this->assertEquals($newPassword, $user->password());
    }

    /**
    * @test
    */
    public function itShouldEditUsername()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newUsername = 'jennypenny';

        $user->editUsername($newUsername);

        $this->assertEquals($newUsername, $user->username());
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
        $user = new User($forename, $surname, 'Hase', $email, new Role($roleName), '12345678910', new UserId());

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
        $user = new User($forename, $surname, 'Hase', $email, $role, '12345678910', new UserId());

        $user->approve();
        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());

        $user->disapprove();
        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }
}
