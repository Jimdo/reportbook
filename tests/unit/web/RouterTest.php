<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\Router;
use Jimdo\Reports\functional\Web\Controller\FixtureController;

class RouterTest extends TestCase
{
    /** @var Router */
    private $router;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /** @var array */
    private $queryParams = [];

    /** @var array */
    private $formData = [];

    /** @var array */
    private $sessionData = [];

    protected function setUp()
    {
        $this->applicationConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');
        $this->router = new Router(
            $this->applicationConfig,
            [
                'defaultRequestObject' => new Request(
                    $this->queryParams,
                    $this->formData,
                    $this->sessionData
                ),
                'defaultResponseObject' => new Response()
            ]
        );
    }

    /**
     * @test
     */
    public function itShouldDispatchToControllerAndAction()
    {
        $uri = "/fixture/test";
        $this->assertEquals('testAction called', $this->router->dispatch($uri));
    }

    /**
     * @test
     */
    public function itShouldDispatchToIndexAction()
    {
        $uri = "/fixture";
        $this->assertEquals('indexAction called', $this->router->dispatch($uri));
    }

    /**
     * @test
     */
    public function itShouldConfigureDefaultControllerAndDefaultAction()
    {
        $router = new Router(
            $this->applicationConfig,
            [
                'defaultController' => 'default',
                'defaultAction' => 'default',
                'defaultRequestObject' => new Request(
                    $this->queryParams,
                    $this->formData,
                    $this->sessionData
                ),
                'defaultResponseObject' => new Response()
            ]
        );

        $this->assertEquals(
            'Jimdo\Reports\Web\Controller\DefaultController',
            $router->defaultController()
        );

        $this->assertEquals('defaultAction', $router->defaultAction());
    }

    /**
     * @test
     */
    public function itShouldHaveIndexControllerAndIndexActionAsDefault()
    {
        $this->assertEquals(
            'Jimdo\Reports\Web\Controller\IndexController',
            $this->router->defaultController()
        );

        $this->assertEquals('indexAction', $this->router->defaultAction());
    }

    /**
     * @test
     */
    public function itShouldDispatchToDefaultControllerAndDefaultAction()
    {
        $uri = "/";
        $router = new Router(
            $this->applicationConfig,
            [
                'defaultController' => 'fixture',
                'defaultAction' => 'default',
                'defaultRequestObject' => new Request(
                    $this->queryParams,
                    $this->formData,
                    $this->sessionData
                ),
                'defaultResponseObject' => new Response()
            ]
        );
        $this->assertEquals('defaultAction called', $router->dispatch($uri));
    }

    /**
     * @test
     */
    public function itShouldIgnoreQueryParamsInUri()
    {
        $uri = "/fixture/test?report=Hase";
        $this->assertEquals('testAction called', $this->router->dispatch($uri));
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\Web\ControllerNotFoundException
     */
    public function itShouldThrowControllerNotFoundException()
    {
        $uri = "favicon.ico";
        $this->router->dispatch($uri);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\Web\ActionNotFoundException
     */
    public function itShouldThrowActionNotFoundException()
    {
        $uri = "/fixture/hase";
        $this->router->dispatch($uri);
    }

    /**
     * @test
     */
    public function itShouldAddPathToIgnoreList()
    {
        $path = 'css/';
        $this->router->ignorePath($path);
        $this->assertEquals($this->router->ignoreList(), ['/' . $path]);
    }

    /**
     * @test
     */
    public function itShouldIgnorePathsOnList()
    {
        $path = 'css/';
        $this->router->ignorePath($path);

        $uri = "/css/blabla";

        $this->assertEquals(null ,$this->router->dispatch($uri));
    }
}
