<?php

namespace Jimdo\Reports\Web\Validator;

use PHPUnit\Framework\TestCase;

class StringValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateString()
    {
        $validator = new StringValidator();

        $this->assertTrue($validator->isValid('hase'));
        $this->assertTrue($validator->isValid(''));

        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid(123));
        $this->assertFalse($validator->isValid(123.123));
        $this->assertFalse($validator->isValid(new \stdClass()));
        $this->assertFalse($validator->isValid(true));
        $this->assertFalse($validator->isValid(false));
    }
}
