<?php

namespace Jimdo\Reports\User\PasswordConstraints;

use PHPUnit\Framework\TestCase;

class PasswordLengthTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCheckPasswordLenght()
    {
        $passwordLength = new PasswordLength();

        $wrongPassword = 'some';
        $this->assertFalse($passwordLength->check($wrongPassword));

        $correctPassword = 'somepassword';
        $this->assertTrue($passwordLength->check($correctPassword));
    }
}
