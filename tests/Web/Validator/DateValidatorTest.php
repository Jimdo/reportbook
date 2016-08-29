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

        $this->assertTrue($validator->isValid('1.1.2000'));
        $this->assertTrue($validator->isValid('01.2.2000'));
        $this->assertTrue($validator->isValid('01.10.2000'));
        $this->assertTrue($validator->isValid('01.01.00'));
        $this->assertTrue($validator->isValid('01.12.00'));

        $this->assertFalse($validator->isValid('13.13.16'));
        $this->assertFalse($validator->isValid('32.08.16'));
        $this->assertFalse($validator->isValid('-12.8.16'));
        $this->assertFalse($validator->isValid('12.-8.16'));
        $this->assertFalse($validator->isValid('12.8.-16'));
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessage()
    {
        $validator = new DateValidator();

        $date = '13.13.16';
        $validator->isValid($date);

        $this->assertEquals("Month of '$date' is not between 1 and 12", $validator->errorMessage());

        $date = '32.13.16';
        $validator->isValid($date);

        $this->assertEquals("Day of '$date' is not between 1 and 31", $validator->errorMessage());

        $date = '12.12.-1';
        $validator->isValid($date);

        $this->assertEquals("Year of '$date' is before christ", $validator->errorMessage());
    }
}
