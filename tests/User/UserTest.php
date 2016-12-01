<?php

namespace Jimdo\Reports\User;

use Jimdo\Repors\User\PasswordStrategy\Hashed;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
    * @test
    */
    public function itShouldHaveUserConstruct()
    {
        $optionsUsed = [];
        $user = $this->user([], $optionsUsed);

        $this->assertEquals($optionsUsed['email'], $user->email());
        $this->assertEquals($optionsUsed['password'], $user->password());
        $this->assertInternalType('string', $user->id());
        $this->assertEquals($optionsUsed['role']->name(), $user->roleName());
        $this->assertEquals($optionsUsed['isHashedPassword'], $user->isHashedPassword());
        $this->assertEquals($optionsUsed['username'], $user->username());
    }

    /**
    * @test
    */
    public function itShouldEditPassword()
    {
        $user = $this->user();

        $newPassword = 'newPassword123';

        $user->editPassword($user->password(), $newPassword);

        $strategy = new PasswordStrategy\Hashed();

        $this->assertTrue($strategy->verify($newPassword, $user->password()));
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\PasswordException
     */
    public function itShouldThrowExceptionForViolationOfConstraints()
    {
        $user = $this->user();

        $invalidPassword = 'abc';
        $user->editPassword($user->password(), $invalidPassword);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\User\PasswordException
     */
    public function itShouldThrowExceptionOnUnverifiedOldPassword()
    {
        $user = $this->user();

        $oldPassword = 'some wrong old password';
        $newPassword = 'password to be set';

        $user->editPassword($oldPassword, $newPassword);
    }

    /**
    * @test
    */
    public function itShouldEditUsername()
    {
        $user = $this->user();

        $newUsername = 'jennypenny';

        $user->editUsername($newUsername);

        $this->assertEquals($newUsername, $user->username());
    }

    /**
    * @test
    */
    public function itShouldEditEmail()
    {
        $user = $this->user();

        $newEmail = 'jenny@hotmail.de';

        $user->editEmail($newEmail);

        $this->assertEquals($newEmail, $user->email());
    }

    /**
    * @test
    */
    public function itShouldHaveRoleConstruct()
    {
        $user = $this->user();

        $this->assertEquals('trainee', $user->roleName());

        $this->assertEquals(Role::STATUS_NOT_APPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldChangeStatusOfRole()
    {
        $user = $this->user();

        $user->approve();
        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());

        $user->disapprove();
        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldVerifyPasswordWithCorrectStrategy()
    {
        $clearTextPassword = 'SomeClearTextPassword123';
        $user = $this->user([
            'password' => $clearTextPassword,
            'isHashedPassword' => false
        ]);

        $this->assertTrue($user->verify($clearTextPassword));

        $hashed = new PasswordStrategy\Hashed();

        $password = 'some encrypted Password123';
        $encryptedPassword = $hashed->encrypt($password);
        $user = $this->user([
            'password' => $encryptedPassword,
            'isHashedPassword' => true
        ]);

        $this->assertTrue($user->verify($password));
    }

    /**
     * @test
     */
    public function itShouldEnableHashedPassword()
    {
        $user = $this->user();

        $this->assertFalse($user->isHashedPassword());

        $user->enableHashedPassword();

        $this->assertTrue($user->isHashedPassword());
    }

    /**
     * @test
     */
    public function theAdminUserShouldHaveRoleAdmin()
    {
        $role = ['role' => new Role(Role::ADMIN)];
        $user = $this->user($role);

        $this->assertEquals($role['role']->name(), $user->roleName());
    }

    /**
     * @param array $options
     * @param array $optionsUsed
     * @return User
     */
    private function user(array $options = [], array &$optionsUsed = []): User
    {
        $defaults = [
            'username' => 'max_mustermann',
            'email' => 'max.mustermann@hotmail.de',
            'role' => new Role('trainee'),
            'password' => 'SecurePassword123',
            'userId' => new UserId(),
            'isHashedPassword' => false
        ];

        $optionsUsed = array_merge($defaults, $options);

        return $user = new User(
            $optionsUsed['username'],
            $optionsUsed['email'],
            $optionsUsed['role'],
            $optionsUsed['password'],
            $optionsUsed['userId'],
            $optionsUsed['isHashedPassword']
        );
    }
}
