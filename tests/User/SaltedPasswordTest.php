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
        $saltedPassword = new SaltedPassword();

        $password = 'dasIstEinSuperHartesPasswort';
        $hash = $saltedPassword->encrypt($password);

        $this->assertTrue($saltedPassword->verify($password, $hash));
    }
}
