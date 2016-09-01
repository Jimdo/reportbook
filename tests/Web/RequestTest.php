<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldGetQueryParams()
    {
        $queryParams = [
            'hase' => '1',
            'igel' => 'fuchs'
        ];
        $formData = [];
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);

        $this->assertEquals($queryParams, $request->getQueryParams());
    }

    /**
     * @test
     */
    public function itShouldGetFormData()
    {
        $queryParams = [];
        $sessionData = [];
        $formData = [
            'hase' => '1',
            'igel' => 'fuchs'
        ];

        $request = new Request($queryParams, $formData, $sessionData);

        $this->assertEquals($formData, $request->getFormData());
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
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);

        $this->assertEquals($queryParams['hase'], $request->getQueryParams('hase'));
        $this->assertEquals($queryParams['igel'], $request->getQueryParams('igel'));
    }

    /**
     * @test
     */
    public function itShouldGetSpecificFormData()
    {
        $queryParams = [];
        $sessionData = [];
        $formData = [
            'hase' => '1',
            'igel' => 'fuchs'
        ];

        $request = new Request($queryParams, $formData, $sessionData);

        $this->assertEquals($formData['hase'], $request->getFormData('hase'));
        $this->assertEquals($formData['igel'], $request->getFormData('igel'));
    }

    /**
     * @test
     */
    public function itShouldGetDefaultQueryParam()
    {
        $queryParams = [];
        $formData = [];
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);

        $this->assertEquals('hase', $request->getQueryParams('not_found', 'hase'));
    }

    /**
     * @test
     */
    public function itShouldGetDefaultFormData()
    {
        $queryParams = [];
        $formData = [];
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);

        $this->assertEquals('default', $request->getFormData('not_found', 'default'));
    }

    /**
     * @test
     */
    public function itShouldGetSessionData()
    {
        $queryParams = [];
        $formData = [];
        $sessionData = [
            'hase' => '1',
            'igel' => 'fuchs'
        ];

        $request = new Request($queryParams, $formData, $sessionData);

        $this->assertEquals($sessionData['hase'], $request->getSessionData('hase'));
        $this->assertEquals($sessionData['igel'], $request->getSessionData('igel'));
    }
}
