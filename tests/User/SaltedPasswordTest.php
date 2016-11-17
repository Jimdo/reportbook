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

        $salt = $saltedPassword->generateSalt('DasIstMeinPasswort');

        echo $salt;
        $this->assertInternalType('string', $salt);
    }
}
