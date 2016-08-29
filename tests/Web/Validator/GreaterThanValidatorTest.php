<?php

namespace Jimdo\Reports\Web\Validator;

use PHPUnit\Framework\TestCase;

class GreaterThanValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateGreaterThan()
    {
        $greaterThan = 3;
        $validator = new GreaterThanValidator($greaterThan);

        $this->assertFalse($validator->isValid(-1));
        $this->assertFalse($validator->isValid(0));
        $this->assertFalse($validator->isValid(1));
        $this->assertFalse($validator->isValid(2));
        $this->assertFalse($validator->isValid(3));

        $this->assertTrue($validator->isValid(4));

        $this->assertFalse($validator->isValid("4"));
        $this->assertFalse($validator->isValid("100"));
    }
}
