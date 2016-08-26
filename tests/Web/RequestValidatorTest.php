<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAddFieldValidations()
    {
        $request = [
            'name' => 'Max Mustermann',
            'age' => 32,
            'height' => 178.5,
            'isCustomer' => false,
            'birthday' => '11.07.1973',
        ];

        $validator = new RequestValidator();

        $validator->add('name', 'string');
        $validator->add('age', 'integer');
        $validator->add('height', 'float');
        $validator->add('isCustomer', 'bool');

        $this->assertTrue($validator->isValid($request));
    }
}
