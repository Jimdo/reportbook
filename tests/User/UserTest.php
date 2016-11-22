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
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'strongpassword';
        $isHashedPassword = false;
        $user = new User('Hase', $email, $role, $password, new UserId(), $isHashedPassword);

        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertInternalType('string', $user->id());
        $this->assertEquals($role->name(), $user->roleName());
        $this->assertEquals($isHashedPassword, $user->isHashedPassword());
        $this->assertEquals('Hase', $user->username());
    }

    /**
    * @test
    */
    public function itShouldEditPassword()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $oldPassword = '1111111';
        $isHashedPassword = false;
        $user = new User('Hase', $email, $role, $oldPassword, new UserId(), $isHashedPassword);

        $newPassword = 'peterlustig';

        $user->editPassword($oldPassword, $newPassword);

        $this->assertEquals($newPassword, $user->password());
    }

    /**
    * @test
    */
    public function itShouldEditUsername()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1111111';
        $username = 'jenny';
        $isHashedPassword = false;
        $user = new User('Hase', $email, $role, $password, new UserId(), $isHashedPassword);

        $newUsername = 'jennypenny';

        $user->editUsername($newUsername);

        $this->assertEquals($newUsername, $user->username());
    }

    /**
    * @test
    */
    public function itShouldEditEmail()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1111111';
        $username = 'jenny';
        $isHashedPassword = false;
        $user = new User('Hase', $email, $role, $password, new UserId(), $isHashedPassword);

        $newEmail = 'jenny@hotmail.de';

        $user->editEmail($newEmail);

        $this->assertEquals($newEmail, $user->email());
    }

    /**
    * @test
    */
    public function itShouldHaveRoleConstruct()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '12345678910';
        $isHashedPassword = false;
        $user = new User('Hase', $email, $role, $password, new UserId(), $isHashedPassword);

        $this->assertEquals('trainee', $user->roleName());

        $this->assertEquals(Role::STATUS_NOT_APPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldChangeStatusOfRole()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '12345678910';
        $isHashedPassword = false;
        $user = new User('Hase', $email, $role, $password, new UserId(), $isHashedPassword);

        $user->approve();
        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());

        $user->disapprove();
        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }
}
