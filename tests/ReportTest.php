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

    /**
     * @test
     */
    public function itShouldHaveStatusNewAfterCreate()
    {
        $trainee = new Trainee('Max');
        $content = 'some content';
        $report = new Report($trainee, $content);

        $this->assertEquals(Report::STATUS_NEW, $report->status());
    }

    /**
     * @test
     */
    public function itShouldApproveReport()
    {
        $trainee = new Trainee('Max');
        $content = 'some content';
        $report = new Report($trainee, $content);

        $report->approve();
        $this->assertEquals(Report::STATUS_APPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldDisapproveReport()
    {
        $trainee = new Trainee('Max');
        $content = 'some content';
        $report = new Report($trainee, $content);

        $report->disapprove();
        $this->assertEquals(Report::STATUS_DISAPPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldRequestApproval()
    {
        $trainee = new Trainee('Max');
        $content = 'some content';
        $report = new Report($trainee, $content);

        $report->requestApproval();
        $this->assertEquals(Report::STATUS_APPROVAL_REQUESTED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldHaveId()
    {
        $trainee = new Trainee('Max');
        $content = 'some content';
        $report = new Report($trainee, $content);

        $this->assertInternalType('string', $report->id());

        $report1 = new Report($trainee, $content);
        $this->assertInternalType('string', $report->id());

        $this->assertNotEquals($report->id(), $report1->id());
    }
}
