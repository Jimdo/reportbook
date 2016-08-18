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
        $traineeId = uniqid();
        $content = 'some content';
        $report = new Report($traineeId, $content);

        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report = new Report($traineeId, $content);

        $this->assertEquals($content, $report->content());
    }

    /**
     * @test
     */
    public function itShouldEditContent()
    {
        $traineeId = uniqid();
        $content = 'some content';
        $report = new Report($traineeId, $content);

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
    public function itShouldHaveTraineeId()
    {
        $traineeId = uniqid();
        $report = new Report($traineeId, 'some content');

        $this->assertEquals($traineeId, $report->traineeId());
    }

    /**
     * @test
     */
    public function itShouldHaveStatusNewAfterCreate()
    {
        $traineeId = uniqid();
        $content = 'some content';
        $report = new Report($traineeId, $content);

        $this->assertEquals(Report::STATUS_NEW, $report->status());
    }

    /**
     * @test
     */
    public function itShouldApproveReport()
    {
        $traineeId = uniqid();
        $content = 'some content';
        $report = new Report($traineeId, $content);

        $report->approve();
        $this->assertEquals(Report::STATUS_APPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldDisapproveReport()
    {
        $traineeId = uniqid();
        $content = 'some content';
        $report = new Report($traineeId, $content);

        $report->disapprove();
        $this->assertEquals(Report::STATUS_DISAPPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldRequestApproval()
    {
        $traineeId = uniqid();
        $content = 'some content';
        $report = new Report($traineeId, $content);

        $report->requestApproval();
        $this->assertEquals(Report::STATUS_APPROVAL_REQUESTED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldHaveId()
    {
        $traineeId = uniqid();
        $content = 'some content';
        $report = new Report($traineeId, $content);

        $this->assertInternalType('string', $report->id());

        $report1 = new Report($traineeId, $content);
        $this->assertInternalType('string', $report->id());

        $this->assertNotEquals($report->id(), $report1->id());
    }
}
