<?php

namespace Jimdo\Reports\User\PasswordConstraints;

use PHPUnit\Framework\TestCase;

class PasswordLowerCaseTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCheckIfPasswordContainsAtLeastOneLowerCase()
    {
        $passwordUpperCase = new PasswordLowerCase();

        $correctPassword = 'RIGHTpASSWORD';
        $this->assertTrue($passwordUpperCase->check($correctPassword));

        $wrongPassword = 'WRONGPASSWORD';
        $this->assertFalse($passwordUpperCase->check($wrongPassword));
    }
}
