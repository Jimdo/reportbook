<?php

namespace Jimdo\Reports\User\PasswordConstraints;

use PHPUnit\Framework\TestCase;

class PasswordNumbersTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCheckIfPasswordContainsAtLeastTwoNumbers()
    {
        $passwordNumbers = new PasswordNumbers();

        $correctPassword = 'password12';
        $this->assertTrue($passwordNumbers->check($correctPassword));

        $wrongPassword = 'password1';
        $this->assertFalse($passwordNumbers->check($wrongPassword));
    }
}
