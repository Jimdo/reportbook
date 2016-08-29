<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Web\View as View;

class ViewTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldRenderGivenTemplates()
    {
        $view = new View('tests/Web/ViewFixture.php');
        $view->name = $expectedName = 'Horst';
        $view->content = $expectedContent = 'ABC';

        $expected = "test 123\n<h1>$expectedName</h1>\n<p>$expectedContent</p>\n<h2>$expectedName</h2>\n";
        $this->assertEquals($expected, $view->render());
    }
}
