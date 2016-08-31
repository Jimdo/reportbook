<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldDispatchToControllerAndAction()
    {
        $uri = "/fixture/test";
        $router = new Router();
        $this->assertEquals('testAction called', $router->dispatch($uri));
    }

    /**
     * @test
     */
    public function itShouldDispatchToIndexAction()
    {
        $uri = "/fixture";
        $router = new Router();
        $this->assertEquals('indexAction called', $router->dispatch($uri));
    }

    /**
     * @test
     */
    public function itShouldConfigureDefaultControllerAndDefaultAction()
    {
        $router = new Router([
            'defaultController' => 'default',
            'defaultAction' => 'default'
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
        $router = new Router();

        $this->assertEquals(
            'Jimdo\Reports\Web\Controller\IndexController',
            $router->defaultController()
        );

        $this->assertEquals('indexAction', $router->defaultAction());
    }

    /**
     * @test
     */
    public function itShouldDispatchToDefaultControllerAndDefaultAction()
    {
        $uri = "/";
        $router = new Router([
            'defaultController' => 'fixture',
            'defaultAction' => 'default'
        ]);

        $this->assertEquals('defaultAction called', $router->dispatch($uri));
    }

    /**
     * @test
     */
    public function itShouldIgnoreQueryParamsInUri()
    {
        $uri = "/fixture/test?report=Hase";
        $router = new Router();
        $this->assertEquals('testAction called', $router->dispatch($uri));
    }

}
