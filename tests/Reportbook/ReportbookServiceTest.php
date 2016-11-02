<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\Views\Report as ReadOnlyReport;
use Jimdo\Reports\Reportbook\CommentFakeRepository as CommentFakeRepository;
use Jimdo\Reports\Reportbook\CommentService as CommentService;

class ReportbookServiceTest extends TestCase
{
    /** @var ReportbookService */
    private $reportbookService;

    /** @var ReportRepository */
    private $reportRepository;

    /** @var CommentRepository */
    private $commentRepository;

    /** @var CommentService */
    private $commentService;

    protected function setUp()
    {
        $this->reportRepository = new ReportFakeRepository();

        $this->commentRepository = new CommentFakeRepository();
        $this->commentService = new CommentService($this->commentRepository);

        $this->reportbookService = new ReportbookService($this->reportRepository, $this->commentService);
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $expectedTraineeId = new TraineeId();
        $expectedContent = 'some content';

        $report = $this->reportbookService->createReport($expectedTraineeId, $expectedContent, '10.10.10', '34');

        $this->assertInstanceOf('\Jimdo\Reports\Views\Report', $report);

        $createdReport = $this->reportRepository->reports[0];

        $this->assertEquals($expectedTraineeId->id(), $createdReport->traineeId());
        $this->assertEquals($expectedContent, $createdReport->content());
    }

    /**
     * @test
     */
    public function itShouldEditReport()
    {
        $traineeId = new TraineeId();
        $content = 'some content';

        $report = $this->reportbookService->createReport($traineeId, $content, '10.10.10', '34');

        $expectedContent = 'some modified content';
        $this->reportbookService->editReport($report->id(), $expectedContent, '10.10.10', '34');

        $this->assertEquals($expectedContent, $report->content());
    }

    /**
     * @test
     */
    public function itShouldHaveNewCalendarweekAndDateToEditReport()
    {
        $traineeId = new TraineeId();
        $content = 'some content';

        $report = $this->reportbookService->createReport($traineeId, $content, '10.10.10', '34');

        $expectedContent = 'some modified content';
        $expectedDate = '20.12.2012';
        $expectedWeek = '20';

        $this->reportbookService->editReport($report->id(), $expectedContent, $expectedDate, $expectedWeek);

        $this->assertEquals($expectedDate, $report->date());
        $this->assertEquals($expectedWeek, $report->calendarWeek());
    }

    /**
     * @test
     */
    public function itShouldReturnAllReports()
    {
        $tomId = new TraineeId();
        $jennyId = new TraineeId();

        $tomsContent = "tom's content";
        $jennysContent = "jenny's content";

        $this->assertCount(0, $this->reportbookService->findAll());

        $this->reportbookService->createReport($tomId, $tomsContent, '10.10.10', '34');
        $this->reportbookService->createReport($jennyId, $jennysContent, '10.10.10', '34');

        $reports = $this->reportbookService->findAll();
        $this->assertCount(2, $reports);

        foreach ($reports as $report) {
            $this->assertInstanceOf('\Jimdo\Reports\Views\Report', $report);
        }
    }

    /**
     * @test
     */
    public function itShouldReturnDateOfReport()
    {
        $expectedTraineeId = new TraineeId();
        $expectedContent = 'some content';
        $expectedDate = '23.08.2016';

        $report = $this->reportbookService->createReport($expectedTraineeId, $expectedContent, $expectedDate, '34');

        $this->assertEquals($expectedDate, $report->date());
    }

    /**
     * @test
     */
    public function itShouldReturnCalendarWeek()
    {
        $expectedTraineeId = new TraineeId();
        $expectedContent = 'some content';
        $expectedDate = '23.08.2016';
        $expectedWeek = '34';


        $report = $this->reportbookService->createReport($expectedTraineeId, $expectedContent, $expectedDate, $expectedWeek);

        $this->assertEquals($expectedWeek, $report->calendarWeek());
    }

    /**
     * @test
     */
    public function itShouldReturnAllReportsForTraineeId()
    {
        $tomId = new TraineeId();
        $jennyId = new TraineeId();

        $this->assertCount(0, $this->reportbookService->findByTraineeId($tomId->id()));
        $this->assertCount(0, $this->reportbookService->findByTraineeId($jennyId->id()));

        $this->reportbookService->createReport($tomId, 'some content', '10.10.10', '34');
        $this->reportbookService->createReport($jennyId, 'some content', '10.10.10', '34');

        $tomsReports = $this->reportbookService->findByTraineeId($tomId->id());
        $jennysReports = $this->reportbookService->findByTraineeId($jennyId->id());

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
        $traineeId = new TraineeId();

        $report = $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');
        $this->assertCount(1, $this->reportbookService->findAll());

        $this->reportbookService->deleteReport($report->id());
        $this->assertCount(0, $this->reportbookService->findAll());
    }

    /**
     * @test
     */
    public function itShouldFindById()
    {
        $traineeId = new TraineeId();

        $report = $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');
        $reportId = $report->id();

        $foundReport = $this->reportbookService->findById($reportId, $traineeId->id());

        $this->assertEquals($report->content(), $foundReport->content());
        $this->assertEquals($report->traineeId(), $foundReport->traineeId());
    }

    /**
     * @test
     */
    public function itShouldOnlyFindReportOfMatchingTrainee()
    {
        $traineeId = new TraineeId();
        $wrongTraineeId = new TraineeId();

        $report = $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');

        $foundReport = $this->reportbookService->findById($report->id(), $wrongTraineeId->id());

        $this->assertNull($foundReport);
    }

    /**
     * @test
     */
    public function itShouldRequestApproval()
    {
        $traineeId = new TraineeId();

        $report = $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');

        $this->reportbookService->requestApproval($report->id());
        $this->assertEquals(Report::STATUS_APPROVAL_REQUESTED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldSaveStateAfterRequestApproval()
    {
        $traineeId = new TraineeId();

        $report = $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');

        $this->reportRepository->saveMethodCalled = false;

        $this->reportbookService->requestApproval($report->id());

        $this->assertTrue($this->reportRepository->saveMethodCalled);
    }

    /**
     * @test
     */
    public function itShouldApproveReport()
    {
        $traineeId = new TraineeId();

        $report= $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');
        $reportId = $report->id();

        $this->reportbookService->approveReport($reportId);

        $this->assertEquals(Report::STATUS_APPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldDisapproveReport()
    {
        $traineeId = new TraineeId();

        $report= $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');
        $reportId = $report->id();

        $this->reportbookService->disapproveReport($reportId);

        $this->assertEquals(Report::STATUS_DISAPPROVED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldReturnReportsByStatus()
    {
        $traineeId = new TraineeId();

        $expectedReports = [];
        $expectedReports[] = $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');
        $expectedReports[] = $this->reportbookService->createReport($traineeId, 'some other content', '10.10.10', '34');

        $reports = $this->reportbookService->findByStatus(Report::STATUS_NEW);

        $this->assertEquals($expectedReports[0]->status(), $reports[0]->status());
        $this->assertEquals($expectedReports[1]->status(), $reports[1]->status());

        $expectedReports = [];
        $expectedReports[] = $this->reportbookService->createReport($traineeId, 'some content', '10.10.10', '34');
        $expectedReports[] = $this->reportbookService->createReport($traineeId, 'some other content', '10.10.10', '34');

        $this->reportbookService->requestApproval($expectedReports[0]->id());
        $this->reportbookService->requestApproval($expectedReports[1]->id());

        $reports = $this->reportbookService->findByStatus(Report::STATUS_APPROVAL_REQUESTED);

        $this->assertEquals($expectedReports[0]->status(), $reports[0]->status());
        $this->assertEquals($expectedReports[1]->status(), $reports[1]->status());
    }

    /**
     * @test
     */
    public function itShouldCreateComment()
    {
        $date = 'Date';
        $content = 'some content';
        $reportId = uniqid();
        $userId = uniqid();

        $comment = $this->reportbookService->createComment($reportId, $userId, $date, $content);

        $this->assertInstanceOf('\Jimdo\Reports\Reportbook\Comment', $comment);

        $createdComment = $this->commentRepository->comments[0];

        $this->assertEquals($date, $createdComment->date());
        $this->assertEquals($content, $createdComment->content());
        $this->assertEquals($reportId, $createdComment->reportId());
        $this->assertEquals($userId, $createdComment->userId());
    }

    /**
     * @test
     */
    public function itShouldEditComment()
    {
        $date = 'Date';
        $content = 'some content';
        $reportId = uniqid();
        $userId = uniqid();

        $comment = $this->reportbookService->createComment($reportId, $userId, $date, $content);

        $this->assertInstanceOf('\Jimdo\Reports\Reportbook\Comment', $comment);

        $createdComment = $this->commentRepository->comments[0];

        $newContent = 'Hallo';
        $editedComment = $this->reportbookService->editComment($comment->id(), $newContent, $userId);

        $this->assertEquals($newContent, $editedComment->content());
    }

    /**
     * @test
     */
    public function itShouldFindCommentsByReportId()
    {
        $date = 'Date';
        $content = 'some content';
        $reportId = uniqid();
        $userId = uniqid();

        $foundComments = $this->reportbookService->findCommentsByReportId($reportId);
        $this->assertCount(0, $foundComments);

        $comment1 = $this->reportbookService->createComment($reportId, $userId, $date, $content);
        $comment2 = $this->reportbookService->createComment($reportId, $userId, $date, $content);

        $foundComments = $this->reportbookService->findCommentsByReportId($reportId);
        $this->assertCount(2, $foundComments);
    }

    /**
     * @test
     */
    public function itShouldFindCommentById()
    {
        $date = 'Date';
        $content = 'some content';
        $reportId = uniqid();
        $userId = uniqid();

        $comment = $this->reportbookService->createComment($reportId, $userId, $date, $content);

        $foundComment = $this->reportbookService->findCommentById($comment->id());

        $this->assertEquals($comment->date(), $foundComment->date());
        $this->assertEquals($comment->content(), $foundComment->content());
        $this->assertEquals($comment->reportId(), $foundComment->reportId());
        $this->assertEquals($comment->userId(), $foundComment->userId());
    }

    /**
     * @test
     */
    public function itShouldDeleteComment()
    {
        $date = 'Date';
        $content = 'some content';
        $reportId = uniqid();
        $userId = uniqid();

        $comment = $this->reportbookService->createComment($reportId, $userId, $date, $content);
        $this->assertCount(1, $this->reportbookService->findCommentsByReportId($reportId));

        $this->reportbookService->deleteComment($comment->id());
        $this->assertCount(0, $this->reportbookService->findCommentsByReportId($reportId));
    }
}
