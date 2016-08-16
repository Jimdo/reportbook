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
        $trainee = new Trainee('Max');
        $reportFakeRepository = new ReportFakeRepository();
        $reportBook = new ReportBook($reportFakeRepository);

        $content = 'some content';
        $report = $reportBook->createReport($trainee, $content);

        $this->assertInstanceOf('Jimdo\Reports\Report', $report);
        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report = $reportBook->createReport($trainee, $content);

        $this->assertEquals($content, $report->content());
    }

    /**
     * @test
     */
    public function itShouldSaveReport()
    {
        $trainee = new Trainee('Max');
        $report = new Report($trainee, 'some content');

        $reportFakeRepository = new ReportFakeRepository();
        $this->assertEquals([], $reportFakeRepository->reports);

        $reportBook = new ReportBook($reportFakeRepository);
        $reportBook->save($report);

        $this->assertEquals($report, $reportFakeRepository->reports[0]);
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

        $reportFakeRepository = new ReportFakeRepository();
        $reportBook = new ReportBook($reportFakeRepository);

        $reportBook->save($report1);
        $reportBook->save($report2);

        $this->assertEquals(
            [$report1, $report2],
            $reportBook->findAll()
        );
    }

    /**
     * @test
     */
    public function itShouldReturnAllReportsOfATrainee()
    {
        $reportFakeRepository = new ReportFakeRepository();
        $reportBook = new ReportBook($reportFakeRepository);

        $tom = new Trainee('Tom');
        $jenny = new Trainee('Jenny');

        $report1 = new Report($tom, 'some content');
        $report2 = new Report($jenny, 'some other content');

        $reportBook->save($report1);
        $reportBook->save($report2);

        $this->assertEquals([$report1], $reportBook->findByTrainee($tom));
        $this->assertEquals([$report2], $reportBook->findByTrainee($jenny));
    }
}
