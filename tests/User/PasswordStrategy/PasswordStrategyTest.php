<?php

namespace Jimdo\Reports\User\PasswordStrategy;

use Jimdo\Reports\User\User;
use Jimdo\Reports\User\Role;
use Jimdo\Reports\User\UserId;

use PHPUnit\Framework\TestCase;

class PasswordStrategyTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnClearTextStrategy()
    {
        $strategy = PasswordStrategy::for($this->user(false));
        $this->assertInstanceOf(
            'Jimdo\Reports\User\PasswordStrategy\ClearText',
            $strategy
        );
    }

    /**
     * @test
     */
    public function itShouldReturnHashedStrategy()
    {
        $strategy = PasswordStrategy::for($this->user(true));
        $this->assertInstanceOf(
            'Jimdo\Reports\User\PasswordStrategy\Hashed',
            $strategy
        );
    }

    /**
     * @param bool $isHashedPassword
     * @return User
     */
    private function User(bool $isHashedPassword)
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'strongpassword';
        return new User('Hase', $email, $role, $password, new UserId(), $isHashedPassword);
    }
}
