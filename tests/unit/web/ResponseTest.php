<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     */
    public function itShouldTestHeader()
    {
        $response = new Response();

        $header = 'Content-Type: text/html;charset=UTF-8';
        $response->addHeader($header);

        $header = 'Location: 172.0.0.1;charset=UTF-8';
        $response->addHeader($header);

        $response->render();

        $this->assertContains($header, xdebug_get_headers());
    }
}