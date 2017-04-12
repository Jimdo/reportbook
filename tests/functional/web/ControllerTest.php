<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request;
use Jimdo\Reports\Web\View;
use Jimdo\Reports\Web\Response;
use Jimdo\Reports\Web\RequestValidator;
use Jimdo\Reports\Web\ApplicationConfig;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    public function setUp()
    {
        putenv('APPLICATION_ENV=test');
    }
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

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

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

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

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

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

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

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

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

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

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

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

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
            'role' => 'TRAINEE',
            'authorized' => true
        ];

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

        $this->assertEquals(true, $controller->testIsTrainee(), $twig);
    }

    /**
     * @test
     */
    public function itShouldRenderGivenTemplates()
    {
        $request = new Request([], [], []);
        $requestValidator = new RequestValidator();

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

        $myView = $controller->testView('tests/web/ViewFixture.php');
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

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

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

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

        $controller->testAddRequestValidations();

        $this->assertTrue($controller->testIsRequestValid());
    }

    /**
     * @test
     */
    public function itShouldSayIfSessionNeeded()
    {
        $queryParams = [];
        $formData = [];
        $sessionData = [];

        $applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../src/Web/Controller/Views');
        $twig = new \Twig_Environment($loader);

        $request = new Request($queryParams, $formData, $sessionData);
        $requestValidator = new RequestValidator();
        $controller = new FixtureController($request, $requestValidator, $applicationConfig, new Response(), $twig);

        $action1 = 'Hase';
        $action2 = 'Fuchs';

        $controller->testExcludeFromSession($action1);
        $controller->testExcludeFromSession($action2);

        $this->assertFalse($controller->testNeedSession($action1));
        $this->assertFalse($controller->testNeedSession($action2));

        $this->assertTrue($controller->testNeedSession('Igel'));
    }
}
