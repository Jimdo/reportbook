<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /** @var Router */
    private $router;

    /** @var array */
    private $queryParams = [];

    /** @var array */
    private $formData = [];

    /** @var array */
    private $sessionData = [];

    protected function setUp()
    {
        $this->router = new Router(
            [
                'defaultRequestObject' => new Request(
                    $this->queryParams,
                    $this->formData,
                    $this->sessionData
                )
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
        $router = new Router([
            'defaultController' => 'default',
            'defaultAction' => 'default',
            'defaultRequestObject' => new Request(
                $this->queryParams,
                $this->formData,
                $this->sessionData
            )
        ]);

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
        $router = new Router([
            'defaultController' => 'fixture',
            'defaultAction' => 'default',
            'defaultRequestObject' => new Request(
                $this->queryParams,
                $this->formData,
                $this->sessionData
            )
        ]);

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
