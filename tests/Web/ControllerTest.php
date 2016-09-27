<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
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
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

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
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

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
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

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
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

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
        $sessionData = [];

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

        $this->assertEquals('hase', $controller->testQueryParams('not_found', 'hase'));
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
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

        $this->assertEquals('default', $controller->testFormData('not_found', 'default'));
    }

    /**
     * @test
     */
    public function itShouldCheckIsUserAuthorized()
    {
        $queryParams = [];
        $formData = [];
        $sessionData = [
            'role' => 'Trainee',
            'authorized' => true
        ];

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

        $this->assertEquals(true, $controller->testIsAuthorized('Trainee'));
    }

    /**
     * @test
     */
    public function itShouldRenderGivenTemplates()
    {
        $request = new Request([], [], []);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

        $myView = $controller->testView('tests/Web/ViewFixture.php');
        $myView->name = $expectedName = 'Horst';
        $myView->content = $expectedContent = 'ABC';

        $expected = "test 123\n<h1>$expectedName</h1>\n<p>$expectedContent</p>\n<h2>$expectedName</h2>\n";
        $this->assertEquals($expected, $myView->render());
    }

    /**
     * @test
     */
    public function itShouldAddFieldValidations()
    {
        $formData = [
            'name' => 'Max Mustermann',
            'age' => 32,
        ];

        $request = new Request([], $formData, []);

        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

        $controller->testAddRequestValidations();

        $fields = $controller->requestValidator()->fields();

        $this->assertEquals(
            [
                'name' => 'string',
                'age' => 'integer',
            ],
            $fields
        );
    }

    /**
     * @test
     */
    public function itShouldValidateRequest()
    {
        $formData = [
            'name' => 'Max Mustermann',
            'age' => 32,
        ];

        $request = new Request([], $formData, []);

        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, new ApplicationConfig(__DIR__ . '/../../config.yml'), new Response());

        $controller->testAddRequestValidations();

        $this->assertTrue($controller->testIsRequestValid());
    }
}
