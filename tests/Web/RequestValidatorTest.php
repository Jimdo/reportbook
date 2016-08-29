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
        $validator->add('birthday', 'date');

        $this->assertTrue($validator->isValid($request));

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
        $validator->add('height', 'string');
        $validator->add('isCustomer', 'bool');
        $validator->add('birthday', 'date');

        $this->assertFalse($validator->isValid($request));
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessages()
    {
        $request = [
            'name' => 'Max Mustermann',
            'age' => 32,
            'height' => 178.5,
            'isCustomer' => false,
            'birthday' => '11.07.1973',
        ];

        $validator = new RequestValidator();

        $validator->add('name', 'integer');
        $validator->add('age', 'bool');
        $validator->add('height', 'string');
        $validator->add('isCustomer', 'date');
        $validator->add('birthday', 'float');

        $validator->isValid($request);

        $errorMessages = $validator->errorMessages();

        $this->assertCount(5, $errorMessages);

        $this->assertEquals(
            ['name', 'age', 'height', 'isCustomer', 'birthday'],
            array_keys($errorMessages)
        );
    }
}
