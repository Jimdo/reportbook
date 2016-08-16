<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class TraineeTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveForename()
    {
        $tom = new Trainee('Tom');
        $jenny = new Trainee('Jenny');

        $this->assertEquals('Tom', $tom->forename());
        $this->assertEquals('Jenny', $jenny->forename());
    }
}
