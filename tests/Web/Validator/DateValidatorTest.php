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
}
