<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class ReportBookTest extends TestCase
{
    /** @var ReportBook */
    private $reportBook;

    /** @var ReportRepository */
    private $reportRepository;

    protected function setUp()
    {
        $this->reportRepository = new ReportFakeRepository();
        $this->reportBook = new ReportBook($this->reportRepository);
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $trainee = new Trainee('Max');

        $content = 'some content';
        $report = $this->reportBook->createReport($trainee, $content);

        $this->assertInstanceOf('Jimdo\Reports\Report', $report);
        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report = $this->reportBook->createReport($trainee, $content);

        $this->assertEquals($content, $report->content());
    }

    /**
     * @test
     */
    public function itShouldSaveReport()
    {
        $trainee = new Trainee('Max');
        $report = new Report($trainee, 'some content');

        $this->assertEquals([], $this->reportRepository->reports);

        $this->reportBook->save($report);

        $this->assertEquals($report, $this->reportRepository->reports[0]);
    }

    /**
     * @test
     */
    public function itShouldReturnAllReports()
    {
        $tom = new Trainee('Tom');
        $jenny = new Trainee('Jenny');

        $report1 = new Report($tom, 'some content');
        $report2 = new Report($jenny, 'some other content');

        $this->reportBook->save($report1);
        $this->reportBook->save($report2);

        $this->assertEquals(
            [$report1, $report2],
            $this->reportBook->findAll()
        );
    }

    /**
     * @test
     */
    public function itShouldReturnAllReportsOfATrainee()
    {
        $tom = new Trainee('Tom');
        $jenny = new Trainee('Jenny');

        $report1 = new Report($tom, 'some content');
        $report2 = new Report($jenny, 'some other content');

        $this->reportBook->save($report1);
        $this->reportBook->save($report2);

        $this->assertEquals([$report1], $this->reportBook->findByTrainee($tom));
        $this->assertEquals([$report2], $this->reportBook->findByTrainee($jenny));
    }

    /**
     * @test
     */
    public function itShouldDeleteReport()
    {
        $trainee = new Trainee('Tom');
        $report = new Report($trainee, 'some content');

        $this->reportBook->save($report);
        $this->assertCount(1, $this->reportBook->findAll());

        $this->reportBook->delete($report);
        $this->assertCount(0, $this->reportBook->findAll());
    }
}
