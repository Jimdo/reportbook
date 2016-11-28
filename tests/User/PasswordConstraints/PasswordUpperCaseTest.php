<?php

namespace Jimdo\Reports\User\PasswordConstraints;

use PHPUnit\Framework\TestCase;

class PasswordUpperCaseTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCheckIfPasswordContainsAtLeastOneUpperCase()
    {
        $passwordUpperCase = new PasswordUpperCase();

        $correctPassword = 'rightPassword';
        $this->assertTrue($passwordUpperCase->check($correctPassword));

        $wrongPassword = 'wrongpassword';
        $this->assertFalse($passwordUpperCase->check($wrongPassword));
    }
}
