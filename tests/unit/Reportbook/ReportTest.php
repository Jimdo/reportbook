<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnContent()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $this->assertEquals($content, $report->content());
    }

    /**
     * @test
     */
    public function itShouldEditContent()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $content = 'other content';
        $report->edit($content, '10.10.10',  '34', '2016', Category::SCHOOL);
        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report->edit($content, '10.10.10',  '34', '2016', Category::SCHOOL);
        $this->assertEquals($content, $report->content());
    }

    /**
     * @test
     */
    public function itShouldHaveTraineeId()
    {
        $traineeId = new TraineeId();
        $report = new Report($traineeId, 'some content', '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $this->assertEquals($traineeId->id(), $report->traineeId());
    }

    /**
     * @test
     */
    public function itShouldHaveCalendarYear()
    {
        $traineeId = new TraineeId();
        $report = new Report($traineeId, 'some content', '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $this->assertEquals('2016', $report->calendarYear());
    }

    /**
     * @test
     */
    public function itShouldHaveStatusNewAfterCreate()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $this->assertEquals(Report::STATUS_NEW, $report->status());
    }

    /**
     * @test
     */
    public function itShouldHaveStatusEditedAfterEdit()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $report->edit($content, '10.10.10',  '34', '2016', Category::SCHOOL);
        $this->assertEquals(Report::STATUS_EDITED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldHaveCategory()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $category = Category::SCHOOL;
        $report = new Report($traineeId, $content, '10,10,10', '34', '2016', uniqid(), $category);

        $this->assertEquals($report->category(), Category::SCHOOL);
    }

    /**
     * @test
     */
    public function itShouldApproveReport()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $report->approve();
        $this->assertEquals(Report::STATUS_APPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldDisapproveReport()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $report->disapprove();
        $this->assertEquals(Report::STATUS_DISAPPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldRequestApproval()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $report->requestApproval();
        $this->assertEquals(Report::STATUS_APPROVAL_REQUESTED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldHaveStatusRevisedAfterSavingADisapprovedReport()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $report->requestApproval();
        $this->assertEquals(Report::STATUS_APPROVAL_REQUESTED, $report->status());

        $report->disapprove();
        $this->assertEquals(Report::STATUS_DISAPPROVED, $report->status());

        $report->edit($content, '10.10.10',  '34', '2016', Category::SCHOOL);
        $this->assertEquals(Report::STATUS_REVISED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldHaveId()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $this->assertInternalType('string', $report->id());

        $report1 = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);
        $this->assertInternalType('string', $report->id());

        $this->assertNotEquals($report->id(), $report1->id());
    }

    /**
     * @test
     */
    public function itShouldEditCategory()
    {
        $traineeId = new TraineeId();
        $content = 'some content';
        $report = new Report($traineeId, $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $newCategory = Category::COMPANY;

        $report->edit($content, '10.10.10', '34', '2016', $newCategory);

        $this->assertEquals($newCategory, $report->category());
    }

    /**
     * @test
     */
    public function itShouldOptionalReplaceNewLinesWithBreaksInReportContent()
    {
        $content = "Woche 07\r\n- Montag krank\r\n- Restlicher";

        $report = new Report(new TraineeId(), $content, '10.10.10', '34', '2016', uniqid(), Category::SCHOOL);

        $replaceNewlines = true;
        $formattedContent = $report->content($replaceNewlines);
        $expectedCorrectlyFormattedContent = "Woche 07<br />\r\n- Montag krank<br />\r\n- Restlicher";

        $this->assertEquals($expectedCorrectlyFormattedContent, $formattedContent);
    }
}
