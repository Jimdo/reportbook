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
        $user = new User('Hase', $email, $role, $password, new UserId());

        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertInternalType('string', $user->id());
    }

    /**
    * @test
    */
    public function itShouldEditPassword()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $oldPassword = '1111111';
        $user = new User('Hase', $email, $role, $oldPassword, new UserId());

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
        $user = new User($username, $email, $role, $password, new UserId());

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
        $user = new User($username, $email, $role, $password, new UserId());

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
        $roleName = 'trainee';
        $user = new User('Hase', $email, new Role($roleName), '12345678910', new UserId());

        $this->assertEquals($roleName, $user->roleName());

        $this->assertEquals(Role::STATUS_NOT_APPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldChangeStatusOfRole()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $user = new User('Hase', $email, $role, '12345678910', new UserId());

        $user->approve();
        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());

        $user->disapprove();
        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }
}
