<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class ReportBookTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $reportBook = new ReportBook();
        $content = 'some content';
        $report = $reportBook->createReport($content);

        $this->assertInstanceOf('Jimdo\Reports\Report', $report);
        $this->assertEquals($content, $report->content());
    }
}
