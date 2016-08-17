<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class ReportBookServiceTest extends TestCase
{
    /** @var ReportBookService */
    private $reportBookService;

    /** @var ReportRepository */
    private $reportRepository;

    protected function setUp()
    {
        $this->reportRepository = new ReportFakeRepository();
        $this->reportBookService = new ReportBookService($this->reportRepository);
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $trainee = new Trainee('Max');

        $content = 'some content';
        $report = $this->reportBookService->createReport($trainee, $content);

        $this->assertInstanceOf('Jimdo\Reports\Report', $report);
        $this->assertEquals($content, $report->content());

        $content = 'some other content';
        $report = $this->reportBookService->createReport($trainee, $content);

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

        $this->reportBookService->save($report);

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

        $this->reportBookService->save($report1);
        $this->reportBookService->save($report2);

        $this->assertEquals(
            [$report1, $report2],
            $this->reportBookService->findAll()
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

        $this->reportBookService->save($report1);
        $this->reportBookService->save($report2);

        $this->assertEquals([$report1], $this->reportBookService->findByTrainee($tom));
        $this->assertEquals([$report2], $this->reportBookService->findByTrainee($jenny));
    }

    /**
     * @test
     */
    public function itShouldDeleteReport()
    {
        $trainee = new Trainee('Tom');
        $report = new Report($trainee, 'some content');

        $this->reportBookService->save($report);
        $this->assertCount(1, $this->reportBookService->findAll());

        $this->reportBookService->delete($report);
        $this->assertCount(0, $this->reportBookService->findAll());
    }

    /**
     * @test
     */
    public function itShouldRequestApproval()
    {
        $trainee = new Trainee('Tom');
        $report = new Report($trainee, 'some content');

        $this->reportBookService->requestApproval($report);
        $this->assertEquals(Report::STATUS_APPROVAL_REQUESTED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldReturnReportsByStatus()
    {
        $trainee = new Trainee('Tom');

        $expectedReports = [];
        $expectedReports[] = new Report($trainee, 'some content');
        $expectedReports[] = new Report($trainee, 'some other content');

        $this->reportBookService->save($expectedReports[0]);
        $this->reportBookService->save($expectedReports[1]);

        $reports = $this->reportBookService->findByStatus(Report::STATUS_NEW);
        $this->assertEquals($expectedReports, $reports);

        $expectedReports = [];
        $expectedReports[] = new Report($trainee, 'some content');
        $expectedReports[] = new Report($trainee, 'some other content');

        $expectedReports[0]->requestApproval();
        $expectedReports[1]->requestApproval();

        $this->reportBookService->save($expectedReports[0]);
        $this->reportBookService->save($expectedReports[1]);

        $reports = $this->reportBookService->findByStatus(Report::STATUS_APPROVAL_REQUESTED);
        $this->assertEquals($expectedReports, $reports);
    }
}
