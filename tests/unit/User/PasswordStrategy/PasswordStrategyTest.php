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
    public function itShouldReturnHashedStrategy()
    {
        $strategy = PasswordStrategy::for($this->user());
        $this->assertInstanceOf(
            'Jimdo\Reports\User\PasswordStrategy\Hashed',
            $strategy
        );
    }

    /**
     * @return User
     */
    private function User()
    {
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        return new User('Hase', $email, $role, $password, new UserId());
    }
}
