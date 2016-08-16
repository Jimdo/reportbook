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
        $reportFakeRepository = new ReportFakeRepository();
        $reportBook = new ReportBook($reportFakeRepository);
        $content = 'some content';
        $report = $reportBook->createReport($content);

        $this->assertInstanceOf('Jimdo\Reports\Report', $report);
        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report = $reportBook->createReport($content);

        $this->assertEquals($content, $report->content());
    }

    /**
     * @test
     */
    public function itShouldSaveReport()
    {
        $report = new Report('some content');

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
        $report1 = new Report('some content');
        $report2 = new Report('some other content');

        $reportFakeRepository = new ReportFakeRepository();
        $reportBook = new ReportBook($reportFakeRepository);

        $reportBook->save($report1);
        $reportBook->save($report2);

        $this->assertEquals(
            [$report1, $report2],
            $reportBook->findAll()
        );
    }
}
