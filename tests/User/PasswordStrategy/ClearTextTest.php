<?php

namespace Jimdo\Reports\User\PasswordStrategy;

use PHPUnit\Framework\TestCase;

class ClearTextTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnPasswordAsGiven()
    {
        $clearText = new ClearText();

        $password = 'Hase';

        $this->assertEquals($password, $clearText->encrypt($password));
    }

    /**
    * @test
    */
    public function itShouldVerifyPassword()
    {
        $clearText = new ClearText();

        $password = 'Hase';

        $this->assertTrue($clearText->verify($password, $password));
    }
}
