<?php

namespace Jimdo\Reports\User\PasswordConstraints;

use PHPUnit\Framework\TestCase;

class PasswordConstraintsFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnAllConstraints()
    {
        $constraints = PasswordConstraintsFactory::constraints();

        $this->assertEquals($constraints[0], new PasswordLength());
    }
}
