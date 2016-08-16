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
        $trainee = new Trainee('Max');
        $content = 'some content';
        $report = new Report($trainee, $content);

        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report = new Report($trainee, $content);

        $this->assertEquals($content, $report->content());
    }

    /**
     * @test
     */
    public function itShouldEditContent()
    {
        $trainee = new Trainee('Max');
        $content = 'some content';
        $report = new Report($trainee, $content);

        $content = 'other content';
        $report->edit($content);
        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report->edit($content);
        $this->assertEquals($content, $report->content());
    }

    /**
     * @test
     */
    public function itShouldHaveTrainee()
    {
        $trainee = new Trainee('Max');
        $report = new Report($trainee, 'some content');

        $this->assertEquals($trainee, $report->trainee());
    }
}
