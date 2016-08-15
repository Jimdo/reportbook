<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnContent()
    {
        $content = 'some content';
        $report = new Report($content);

        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report = new Report($content);

        $this->assertEquals($content, $report->content());
    }
}
