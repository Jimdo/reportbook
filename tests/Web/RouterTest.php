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
}
