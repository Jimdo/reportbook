<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnQueryParams()
    {
        $queryParams = [
            'hase' => '1',
            'igel' => 'fuchs'
        ];
        $formData = [];

        $request = new Request($queryParams, $formData);
        $controller = new FixtureController($request);

        $this->assertEquals($queryParams, $controller->testQueryParams());
    }

    /**
     * @test
     */
    public function itShouldReturnFormData()
    {
        $queryParams = [];
        $formData = [
            'hase' => '1',
            'igel' => 'fuchs'
        ];

        $request = new Request($queryParams, $formData);
        $controller = new FixtureController($request);

        $this->assertEquals($formData, $controller->testFormData());
    }

    /**
     * @test
     */
    public function itShouldGetSpecificQueryParam()
    {
        $queryParams = [
            'hase' => '1',
            'igel' => 'fuchs'
        ];
        $formData = [];

        $request = new Request($queryParams, $formData);
        $controller = new FixtureController($request);

        $this->assertEquals($queryParams['hase'], $controller->testQueryParams('hase'));
        $this->assertEquals($queryParams['igel'], $controller->testQueryParams('igel'));
    }

    /**
     * @test
     */
    public function itShouldGetSpecificFormData()
    {
        $queryParams = [];
        $formData = [
            'hase' => '1',
            'igel' => 'fuchs'
        ];

        $request = new Request($queryParams, $formData);
        $controller = new FixtureController($request);

        $this->assertEquals($formData['hase'], $controller->testFormData('hase'));
        $this->assertEquals($formData['igel'], $controller->testFormData('igel'));
    }

    /**
     * @test
     */
    public function itShouldGetDefaultQueryParam()
    {
        $queryParams = [];
        $formData = [];

        $request = new Request($queryParams, $formData);
        $controller = new FixtureController($request);

        $this->assertEquals('hase', $controller->testQueryParams('not_found', 'hase'));
    }

    /**
     * @test
     */
    public function itShouldGetDefaultFormData()
    {
        $queryParams = [];
        $formData = [];

        $request = new Request($queryParams, $formData);
        $controller = new FixtureController($request);

        $this->assertEquals('default', $controller->testFormData('not_found', 'default'));
    }
}
