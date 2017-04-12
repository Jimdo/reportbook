<?php

namespace Jimdo\Reports\Web\Validator;

use PHPUnit\Framework\TestCase;

class BoolValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateBool()
    {
        $validator = new BoolValidator();
        $this->assertFalse($validator->isValid(1));
        $this->assertFalse($validator->isValid(1.1));
        $this->assertFalse($validator->isValid([]));
        $this->assertFalse($validator->isValid(new \stdClass()));
        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid('Bool'));
        $this->assertFalse($validator->isValid(''));

        $this->assertTrue($validator->isValid(true));
        $this->assertTrue($validator->isValid(false));
        $this->assertTrue($validator->isValid('true'));
        $this->assertTrue($validator->isValid('false'));
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessage()
    {
        $validator = new BoolValidator();

        $value = 'Hallo';
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a bool", $validator->errorMessage());

        $value = 1;
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a bool", $validator->errorMessage());

        $value = new \stdClass();
        $validator->isValid($value);

        $value = get_class($value);

        $this->assertEquals("'$value' is not a bool", $validator->errorMessage());
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessageOnlyOnInvalidState()
    {
        $validator = new BoolValidator();
        $validator->isValid(true);
        $this->assertEmpty($validator->errorMessage());

        $validator = new BoolValidator();
        $validator->isValid('false');
        $this->assertEmpty($validator->errorMessage());
    }
}
