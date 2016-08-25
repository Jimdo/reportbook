<?php

namespace Jimdo\Reports\Web\Validator;

use PHPUnit\Framework\TestCase;

class FloatValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateFloat()
    {
        $validator = new FloatValidator();

        $this->assertTrue($validator->isValid(1234.0));
        $this->assertTrue($validator->isValid(0.0));
        $this->assertTrue($validator->isValid(-1234.0));

        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid('hase'));
        $this->assertFalse($validator->isValid(''));
        $this->assertFalse($validator->isValid(123));
        $this->assertFalse($validator->isValid(true));
        $this->assertFalse($validator->isValid(false));
        $this->assertFalse($validator->isValid(new \stdClass()));
    }
}
