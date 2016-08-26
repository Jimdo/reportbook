<?php

namespace Jimdo\Reports\Web\Validator;

use PHPUnit\Framework\TestCase;

class IntegerValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateInteger()
    {
        $validator = new IntegerValidator();
        $this->assertFalse($validator->isValid(1.1));
        $this->assertFalse($validator->isValid(true));
        $this->assertFalse($validator->isValid([]));
        $this->assertFalse($validator->isValid(new \stdClass()));
        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid('Integer'));
        $this->assertFalse($validator->isValid(''));

        $this->assertTrue($validator->isValid(1));
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessage()
    {
        $validator = new IntegerValidator();

        $value = 'Hallo';
        $validator->isValid($value);

        $this->assertEquals("'$value' is not an integer", $validator->errorMessage());

        $value = 1.0;
        $validator->isValid($value);

        $this->assertEquals("'$value' is not an integer", $validator->errorMessage());

        $value = new \stdClass();
        $validator->isValid($value);

        $value = get_class($value);

        $this->assertEquals("'$value' is not an integer", $validator->errorMessage());
    }
}
