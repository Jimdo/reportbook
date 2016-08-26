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
        $this->assertFalse($validator->isValid(1));
        $this->assertFalse($validator->isValid(1.1));
        $this->assertFalse($validator->isValid(true));
        $this->assertFalse($validator->isValid([]));
        $this->assertFalse($validator->isValid(new \stdClass()));
        $this->assertFalse($validator->isValid(null));

        $this->assertTrue($validator->isValid('String'));
        $this->assertTrue($validator->isValid(''));
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessage()
    {
        $validator = new StringValidator();

        $value = 1;
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a string", $validator->errorMessage());

        $value = 1.0;
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a string", $validator->errorMessage());

        $value = new \stdClass();
        $validator->isValid($value);

        $value = get_class($value);

        $this->assertEquals("'$value' is not a string", $validator->errorMessage());
    }
}
