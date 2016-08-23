<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\Views\Report as ReadOnlyReport;

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
        $expectedTraineeId = uniqid();
        $expectedContent = 'some content';

        $report = $this->reportBookService->createReport($expectedTraineeId, $expectedContent);

        $this->assertInstanceOf('\Jimdo\Reports\Views\Report', $report);

        $createdReport = $this->reportRepository->reports[0];

        $this->assertEquals($expectedTraineeId, $createdReport->traineeId());
        $this->assertEquals($expectedContent, $createdReport->content());
    }

    /**
     * @test
     */
    public function itShouldEditReport()
    {
        $traineeId = uniqid();
        $content = 'some content';

        $report = $this->reportBookService->createReport($traineeId, $content);

        $expectedContent = 'some modified content';
        $this->reportBookService->editReport($report->id(), $expectedContent);

        $this->assertEquals($expectedContent, $report->content());
    }

    /**
     * @test
     */
    public function itShouldReturnAllReports()
    {
        $tomId = uniqid();
        $jennyId = uniqid();

        $tomsContent = "tom's content";
        $jennysContent = "jenny's content";

        $this->assertCount(0, $this->reportBookService->findAll());

        $this->reportBookService->createReport($tomId, $tomsContent);
        $this->reportBookService->createReport($jennyId, $jennysContent);

        $reports = $this->reportBookService->findAll();
        $this->assertCount(2, $reports);

        foreach ($reports as $report) {
            $this->assertInstanceOf('\Jimdo\Reports\Views\Report', $report);
        }
    }

    /**
     * @test
     */
    public function itShouldReturnAllReportsForTraineeId()
    {
        $tomId = uniqid();
        $jennyId = uniqid();

        $this->assertCount(0, $this->reportBookService->findByTraineeId($tomId));
        $this->assertCount(0, $this->reportBookService->findByTraineeId($jennyId));

        $this->reportBookService->createReport($tomId, 'some content');
        $this->reportBookService->createReport($jennyId, 'some content');

        $tomsReports = $this->reportBookService->findByTraineeId($tomId);
        $jennysReports = $this->reportBookService->findByTraineeId($jennyId);

        $this->assertCount(1, $tomsReports);
        $this->assertCount(1, $jennysReports);

        foreach ($tomsReports as $report) {
            $this->assertInstanceOf('\Jimdo\Reports\Views\Report', $report);
        }

        foreach ($jennysReports as $report) {
            $this->assertInstanceOf('\Jimdo\Reports\Views\Report', $report);
        }
    }

    /**
     * @test
     */
    public function itShouldDeleteReport()
    {
        $traineeId = uniqid();

        $report = $this->reportBookService->createReport($traineeId, 'some content');
        $this->assertCount(1, $this->reportBookService->findAll());

        $this->reportBookService->deleteReport($report->id());
        $this->assertCount(0, $this->reportBookService->findAll());
    }

    /**
     * @test
     */
    public function itShouldFindById()
    {
        $traineeId = uniqid();

        $report = $this->reportBookService->createReport($traineeId, 'some content');
        $reportId = $report->id();

        $foundReport = $this->reportBookService->findById($reportId, $traineeId);

        $this->assertEquals($report->content(), $foundReport->content());
        $this->assertEquals($report->traineeId(), $foundReport->traineeId());
    }

    /**
     * @test
     */
    public function itShouldOnlyFindReportOfMatchingTrainee()
    {
        $report = $this->reportBookService->createReport('some trainee id', 'some content');

        $foundReport = $this->reportBookService->findById($report->id(), 'some other trainee id');

        $this->assertNull($foundReport);
    }

    /**
     * @test
     */
    public function itShouldRequestApproval()
    {
        $traineeId = uniqid();

        $report = $this->reportBookService->createReport($traineeId, 'some content');

        $this->reportBookService->requestApproval($report->id());
        $this->assertEquals(Report::STATUS_APPROVAL_REQUESTED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldSaveStateAfterRequestApproval()
    {
        $traineeId = uniqid();

        $report = $this->reportBookService->createReport($traineeId, 'some content');

        $this->reportRepository->saveMethodCalled = false;

        $this->reportBookService->requestApproval($report->id());

        $this->assertTrue($this->reportRepository->saveMethodCalled);
    }

    /**
     * @test
     */
    public function itShouldApproveReport()
    {
        $traineeId = uniqid();

        $report= $this->reportBookService->createReport($traineeId, 'some content');
        $reportId = $report->id();

        $this->reportBookService->approveReport($reportId);

        $this->assertEquals(Report::STATUS_APPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldDisapproveReport()
    {
        $traineeId = uniqid();

        $report= $this->reportBookService->createReport($traineeId, 'some content');
        $reportId = $report->id();

        $this->reportBookService->disapproveReport($reportId);

        $this->assertEquals(Report::STATUS_DISAPPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldReturnReportsByStatus()
    {
        $traineeId = uniqid();

        $expectedReports = [];
        $expectedReports[] = $this->reportBookService->createReport($traineeId, 'some content');
        $expectedReports[] = $this->reportBookService->createReport($traineeId, 'some other content');

        $reports = $this->reportBookService->findByStatus(Report::STATUS_NEW);

        $this->assertEquals($expectedReports[0]->status(), $reports[0]->status());
        $this->assertEquals($expectedReports[1]->status(), $reports[1]->status());

        $expectedReports = [];
        $expectedReports[] = $this->reportBookService->createReport($traineeId, 'some content');
        $expectedReports[] = $this->reportBookService->createReport($traineeId, 'some other content');

        $this->reportBookService->requestApproval($expectedReports[0]->id());
        $this->reportBookService->requestApproval($expectedReports[1]->id());

        $reports = $this->reportBookService->findByStatus(Report::STATUS_APPROVAL_REQUESTED);

        $this->assertEquals($expectedReports[0]->status(), $reports[0]->status());
        $this->assertEquals($expectedReports[1]->status(), $reports[1]->status());
    }
}
