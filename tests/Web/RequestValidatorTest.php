<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAddValidationsToFields()
    {
        $request = [
            'name' => 'Mausi',
            'age' => 33,
            'birthday' => '11.09.1983',
        ];

        $validator = new RequestValidator($request);
        $validator->add('name', 'string');
        $validator->add('name', 'stringLength', 5);
        $validator->add('age', 'int');
        $validator->add('age', 'greaterThan', 0);
        $validator->add('birthday', 'date');

        $this->assertTrue($validator->isValid());
    }

    /**
     * @test
     */
    public function itShouldReturnErrorMessages()
    {
        $request = [
            'name' => 'Mausi',
            'age' => '33',
            'birthday' => '11.13.1983',
            'height' => '174.5',
        ];

        $validator = new RequestValidator($request);
        $validator->add('name', 'string');
        $validator->add('name', 'stringLength', 6);
        $validator->add('age', 'int');
        $validator->add('age', 'greaterThan', 35);
        $validator->add('birthday', 'date');
        $validator->add('height', 'float');

        $validator->isValid();

        $this->assertCount(5, $validator->errorMessages());
    }
}
