<?php

namespace Jimdo\Reports\Web\Validator;

use PHPUnit\Framework\TestCase;

class LessThanValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateLessThan()
    {
        $lessThan = 3;
        $validator = new LessThanValidator($lessThan);

        $this->assertTrue($validator->isValid(-1));
        $this->assertTrue($validator->isValid(0));
        $this->assertTrue($validator->isValid(1));
        $this->assertTrue($validator->isValid(2));

        $this->assertFalse($validator->isValid(3));
        $this->assertFalse($validator->isValid(4));

        $this->assertFalse($validator->isValid("4"));
        $this->assertFalse($validator->isValid("100"));
    }
}
