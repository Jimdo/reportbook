<?php

namespace Jimdo\Reports\Web;

use PHPUnit\Framework\TestCase;

class ViewHelperTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldGetStartAndEndDate()
    {
        $week = 6;
        $year = 2016;
        $startDay = 8;

        $viewHelper = new ViewHelper();
        $returnArray = $viewHelper->getStartAndEndDate($week, $year);

        $this->assertEquals($returnArray[2], $startDay);

        $week = 6;
        $year = 1988;
        $startDay = 8;

        $returnArray = $viewHelper->getStartAndEndDate($week, $year);

        $this->assertEquals($returnArray[2], $startDay);
    }

    /**
     * @test
     */
    public function itShouldGenerateUrl()
    {
        $viewHelper = new ViewHelper();

        $controller = 'controllername';
        $action = 'actionname';
        $linkText = 'linkText';
        $queryParams = [ 'baz' => 'bam', 'foo' => 'bar' ];

        $html = $viewHelper->goto($controller, $action, $linkText, $queryParams);

        $assertion = '<a href="/controllername/actionname?baz=bam&foo=bar">linkText</a>';

        $this->assertEquals($assertion, $html);
    }
}
