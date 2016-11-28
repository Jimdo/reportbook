<?php

namespace Jimdo\Reports\User\PasswordConstraints;

use PHPUnit\Framework\TestCase;

class PasswordBlackListTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCheckPasswordBlackList()
    {
        $passwordBlacklist = new PasswordBlackList();

        $wrongPassword = 'Password123';
        $this->assertFalse($passwordBlacklist->check($wrongPassword));

        $correctPassword = 'Paassword123';
        $this->assertTrue($passwordBlacklist->check($correctPassword));
    }
}
