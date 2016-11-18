<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;

class SaltedPasswordTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateSalt()
    {
        $password = 'dasIstEinSuperHartesPasswort';
        $saltedPassword = new SaltedPassword($password);

        $this->assertTrue($saltedPassword->verify($password));
    }
}
