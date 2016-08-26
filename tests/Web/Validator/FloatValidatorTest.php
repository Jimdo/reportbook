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
        $this->assertFalse($validator->isValid(1));
        $this->assertFalse($validator->isValid(true));
        $this->assertFalse($validator->isValid([]));
        $this->assertFalse($validator->isValid(new \stdClass()));
        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid('Float'));
        $this->assertFalse($validator->isValid(''));

        $this->assertTrue($validator->isValid(1.1));
        $this->assertTrue($validator->isValid('1.1'));
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessage()
    {
        $validator = new FloatValidator();

        $value = 'Hallo';
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a float", $validator->errorMessage());

        $value = 1;
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a float", $validator->errorMessage());

        $value = new \stdClass();
        $validator->isValid($value);

        $value = get_class($value);

        $this->assertEquals("'$value' is not a float", $validator->errorMessage());
    }
}
