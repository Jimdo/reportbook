<?php

namespace Jimdo\Reports\User\PasswordStrategy;

use PHPUnit\Framework\TestCase;

class HashedTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnHashedPassword()
    {
        $hashed = new Hashed();

        $password = 'Hase';
        $this->assertNotEquals($password, $hashed->encrypt($password));
    }

    /**
     * @test
     */
    public function itShouldVerifyHashedPassword()
    {
        $hashed = new Hashed();

        $password = 'Hase';
        $hashedPassword = $hashed->encrypt($password);

        $this->assertTrue($hashed->verify($password, $hashedPassword));
    }
}
