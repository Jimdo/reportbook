<?php

namespace Jimdo\Reports\Web\Validator;

use PHPUnit\Framework\TestCase;

class StringLengthValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateStringLength()
    {
        $stringLength = 5;
        $validator = new StringLengthValidator($stringLength);

        $this->assertFalse($validator->isValid('a'));
        $this->assertFalse($validator->isValid('ab'));
        $this->assertFalse($validator->isValid('abc'));
        $this->assertFalse($validator->isValid('abcd'));

        $this->assertTrue($validator->isValid('abcde'));

        $this->assertFalse($validator->isValid('abcdef'));
    }
}
