<?php

namespace Jimdo\Reports\Web\Validator;

use PHPUnit\Framework\TestCase;

class DateValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateDate()
    {
        $validator = new DateValidator();
        $this->assertFalse($validator->isValid(1.1));
        $this->assertFalse($validator->isValid(true));
        $this->assertFalse($validator->isValid([]));
        $this->assertFalse($validator->isValid(new \stdClass()));
        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid('Integer'));
        $this->assertFalse($validator->isValid(''));
        $this->assertFalse($validator->isValid(1));

        $this->assertFalse($validator->isValid('32.11.11'));
        $this->assertFalse($validator->isValid('31.13.11'));
        $this->assertFalse($validator->isValid('31.12.-11'));
        $this->assertFalse($validator->isValid('31.-12.11'));
        $this->assertFalse($validator->isValid('-31.12.11'));
        $this->assertFalse($validator->isValid('1111.12.11'));

        $this->assertTrue($validator->isValid('1.1.11'));
        $this->assertTrue($validator->isValid('01.01.11'));
        $this->assertTrue($validator->isValid('11.11.11'));
        $this->assertTrue($validator->isValid('11.11.00'));
        $this->assertTrue($validator->isValid('11.11.1100'));
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessage()
    {
        $validator = new DateValidator();

        $value = 'Hallo';
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a date", $validator->errorMessage());

        $value = 1.0;
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a date", $validator->errorMessage());

        $value = new \stdClass();
        $validator->isValid($value);

        $value = get_class($value);

        $this->assertEquals("'$value' is not a date", $validator->errorMessage());

        $value = '-31.12.11';
        $validator->isValid($value);

        $this->assertEquals("'$value' is not a date", $validator->errorMessage());
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessageOnlyOnInvalidState()
    {
        $validator = new DateValidator();
        $validator->isValid('29.08.2016');
        $this->assertEmpty($validator->errorMessage());
    }
}
