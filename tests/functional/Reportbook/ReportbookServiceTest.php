<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\functional\Reportbook\CommentFakeRepository;
use Jimdo\Reports\functional\Reportbook\ReportFakeRepository;
use Jimdo\Reports\Reportbook\CommentService;

use Jimdo\Reports\SerializerFactory;
use Jimdo\Reports\Web\ApplicationConfig;

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

        $serializerFactory = new SerializerFactory(new ApplicationConfig(__DIR__ . '/../../../config.yml'));
        $serializer = $serializerFactory->createSerializer();

        $this->reportbookService = new ReportbookService($this->reportRepository, $this->commentService, $serializer);
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $expectedTraineeId = new TraineeId();
        $expectedContent = 'some content';

        $report = $this->reportbookService->createReport($expectedTraineeId, $expectedContent,  '34', '2016', Category::SCHOOL);

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

        $report = $this->reportbookService->createReport($traineeId, $content,  '34', '2016', Category::SCHOOL);

        $expectedContent = 'some modified content';
        $this->reportbookService->editReport($report->id(), $expectedContent,  '34', '2016', Category::SCHOOL);

        $this->assertEquals($expectedContent, $report->content());
    }

    /**
     * @test
     */
    public function itShouldFindNextReport()
    {
        $traineeId = new TraineeId();
        $content = 'some content';

        $currentReport = $this->reportbookService->createReport($traineeId, $content, '34', '2016', Category::SCHOOL);
        $expectedNextReport = $this->reportbookService->createReport($traineeId, $content,  '35', '2016', Category::SCHOOL);

        $this->reportbookService->requestApproval($currentReport->id());
        $this->reportbookService->requestApproval($expectedNextReport->id());

        $nextReport = $this->reportbookService->findNextReport($currentReport->id(), $currentReport->traineeId());

        $this->assertEquals($expectedNextReport->id(), $nextReport->id());
    }

    /**
     * @test
     */
    public function itShouldCalculateNextReportWeekAndYear() {
        $nextCalendarWeekAndYear =  $this->reportbookService->calculateNextReportWeekAndYear(52, 2018);

        $this->assertEquals([1, 2019], $nextCalendarWeekAndYear);

        $nextCalendarWeekAndYear = $this->reportbookService->calculateNextReportWeekAndYear(5, 2015);

        $this->assertEquals([6, 2015], $nextCalendarWeekAndYear);
    }

    /**
     * @test
     */
    public function itShouldHaveNewCalendarweekAndDateToEditReport()
    {
        $traineeId = new TraineeId();
        $content = 'some content';

        $report = $this->reportbookService->createReport($traineeId, $content,  '34', '2016', Category::SCHOOL);

        $expectedContent = 'some modified content';
        $expectedWeek = '20';

        $this->reportbookService->editReport($report->id(), $expectedContent, $expectedWeek, ' 2016', Category::SCHOOL);

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

        $this->reportbookService->createReport($tomId, $tomsContent,  '34', '2016', Category::SCHOOL);
        $this->reportbookService->createReport($jennyId, $jennysContent,  '34', '2016', Category::SCHOOL);

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


        $report = $this->reportbookService->createReport($expectedTraineeId, $expectedContent,  '34', '2016', Category::SCHOOL);

        $this->assertEquals(date('d.m.Y'), $report->date());
    }

    /**
     * @test
     */
    public function itShouldReturnCalendarWeek()
    {
        $expectedTraineeId = new TraineeId();
        $expectedContent = 'some content';
        $expectedWeek = '34';
        $expectedYear = '2016';


        $report = $this->reportbookService->createReport($expectedTraineeId, $expectedContent, $expectedWeek, $expectedYear, Category::SCHOOL);

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

        $this->reportbookService->createReport($tomId, 'some content',  '34', '2016', Category::SCHOOL);
        $this->reportbookService->createReport($jennyId, 'some content',  '34', '2016', Category::SCHOOL);

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

        $report = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
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

        $report = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
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

        $report = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);

        $foundReport = $this->reportbookService->findById($report->id(), $wrongTraineeId->id());

        $this->assertNull($foundReport);
    }

    /**
     * @test
     */
    public function itShouldFindReportsByString()
    {
        $traineeId = new TraineeId();

        $report1 = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
        $report2 = $this->reportbookService->createReport($traineeId, 'other text',  '34', '2016', Category::SCHOOL);
        $report3 = $this->reportbookService->createReport($traineeId, 'no content',  '34', '2016', Category::SCHOOL);

        $foundReports = $this->reportbookService->findReportsByString('content', $traineeId->id(), 'TRAINEE');

        $this->assertCount(2, $foundReports);
        $this->assertEquals('some content', $foundReports[0]->content());
    }

    /**
     * @test
     */
    public function itShouldFindReportsDescending()
    {
        $traineeId1 = new TraineeId('5891bc773d311');
        $traineeId2 = new TraineeId('5891bc773d312');
        $traineeId3 = new TraineeId('5891bc773d313');

        $report1 = $this->reportbookService->createReport($traineeId3, 'some content',  '34', '2016', Category::SCHOOL);
        $report2 = $this->reportbookService->createReport($traineeId1, 'other text',  '34', '2016', Category::SCHOOL);
        $report3 = $this->reportbookService->createReport($traineeId2, 'no content',  '34', '2016', Category::SCHOOL);

        $foundReports = $this->reportRepository->findAll();
        $this->reportbookService->sortArrayDescending('traineeId', $foundReports);

        $this->assertEquals($foundReports[0]->traineeId(), '5891bc773d313');
        $this->assertEquals($foundReports[1]->traineeId(), '5891bc773d312');
        $this->assertEquals($foundReports[2]->traineeId(), '5891bc773d311');
    }

    /**
     * @test
     */
    public function itShouldRequestApproval()
    {
        $traineeId = new TraineeId();

        $report = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);

        $this->reportbookService->requestApproval($report->id());
        $this->assertEquals(Report::STATUS_APPROVAL_REQUESTED, $report->status());
    }

    /**
     * @test
     */
    public function itShouldSaveStateAfterRequestApproval()
    {
        $traineeId = new TraineeId();

        $report = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);

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

        $report= $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
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

        $report= $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
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
        $expectedReports[] = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
        $expectedReports[] = $this->reportbookService->createReport($traineeId, 'some other content',  '34', '2016', Category::SCHOOL);

        $reports = $this->reportbookService->findByStatus(Report::STATUS_NEW);

        $this->assertEquals($expectedReports[0]->status(), $reports[0]->status());
        $this->assertEquals($expectedReports[1]->status(), $reports[1]->status());

        $expectedReports = [];
        $expectedReports[] = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
        $expectedReports[] = $this->reportbookService->createReport($traineeId, 'some other content',  '34', '2016', Category::SCHOOL);

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

        $this->reportbookService->createComment($reportId, $userId, $date, $content);
        $this->reportbookService->createComment($reportId, $userId, $date, $content);

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
    public function itShouldSortReportsByAmmountOfComments()
    {
        $traineeId = new TraineeId();

        $report1 = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
        $report2 = $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);
        $report3= $this->reportbookService->createReport($traineeId, 'some content',  '34', '2016', Category::SCHOOL);

        $date = 'Date';
        $content = 'some content';
        $userId = uniqid();

        $this->reportbookService->createComment($report1->id(), $userId, $date, $content);
        $this->reportbookService->createComment($report1->id(), $userId, $date, $content);

        $this->reportbookService->createComment($report2->id(), $userId, $date, $content);
        $this->reportbookService->createComment($report2->id(), $userId, $date, $content);
        $this->reportbookService->createComment($report2->id(), $userId, $date, $content);

        $this->reportbookService->createComment($report3->id(), $userId, $date, $content);

        $foundReports = $this->reportRepository->findAll();

        $this->reportbookService->sortReportsByAmountOfComments($foundReports);

        $this->assertEquals($foundReports[0]->id(), $report2->id());
        $this->assertEquals($foundReports[1]->id(), $report1->id());
        $this->assertEquals($foundReports[2]->id(), $report3->id());
    }

    /**
     * @test
     */
    public function itShouldSortReportsByStatus()
    {
        $traineeId = new TraineeId();

        $report1 = $this->reportRepository->create($traineeId, 'some content', date('d.m.Y'),  '34', '2016', Category::SCHOOL);
        $report2 = $this->reportRepository->create($traineeId, 'some content', date('d.m.Y'),  '34', '2016', Category::SCHOOL);
        $report3 = $this->reportRepository->create($traineeId, 'some content', date('d.m.Y'),  '34', '2016', Category::SCHOOL);
        $report4 = $this->reportRepository->create($traineeId, 'some content', date('d.m.Y'),  '34', '2016', Category::SCHOOL);
        $report5 = $this->reportRepository->create($traineeId, 'some content', date('d.m.Y'),  '34', '2016', Category::SCHOOL);
        $report6 = $this->reportRepository->create($traineeId, 'some content', date('d.m.Y'),  '34', '2016', Category::SCHOOL);

        $report2->edit('alskd', '11.11.11', '24', '2016', Category::SCHOOL);
        $report3->approve();
        $report4->disapprove();
        $report5->requestApproval();
        $report6->disapprove();
        $report6->edit('alskd', '11.11.11', '24', '2016', Category::SCHOOL);

        $reports = $this->reportRepository->findAll();

        $this->reportbookService->sortReportsByStatus([
                Report::STATUS_APPROVAL_REQUESTED,
                Report::STATUS_REVISED,
                Report::STATUS_DISAPPROVED,
                Report::STATUS_APPROVED,
                Report::STATUS_EDITED,
                Report::STATUS_NEW
            ],
            $reports
        );

        $this->assertEquals($reports[0]->status(), Report::STATUS_APPROVAL_REQUESTED);
        $this->assertEquals($reports[1]->status(), Report::STATUS_REVISED);
        $this->assertEquals($reports[2]->status(), Report::STATUS_DISAPPROVED);
        $this->assertEquals($reports[3]->status(), Report::STATUS_APPROVED);
        $this->assertEquals($reports[4]->status(), Report::STATUS_EDITED);
        $this->assertEquals($reports[5]->status(), Report::STATUS_NEW);
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

        $this->reportbookService->deleteComment($comment->id(), $userId);
        $this->assertCount(0, $this->reportbookService->findCommentsByReportId($reportId));
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\Reportbook\ReportbookServiceException
     */
    public function itShouldThrowExceptionIfUserIsNotAllowedToEditComment()
    {
        $date = 'Date';
        $content = 'some content';
        $reportId = uniqid();
        $userId = uniqid();

        $comment = $this->reportbookService->createComment($reportId, $userId, $date, $content);

        $newContent = 'Hallo';
        $falseUserId = uniqid();

        $this->reportbookService->editComment($comment->id(), $newContent, $falseUserId);
    }

    /**
     * @test
     * @expectedException Jimdo\Reports\Reportbook\ReportbookServiceException
     */
    public function itShouldThrowExceptionIfUserIsNotAllowedToDeleteComment()
    {
        $date = 'Date';
        $content = 'some content';
        $reportId = uniqid();
        $userId = uniqid();

        $comment = $this->reportbookService->createComment($reportId, $userId, $date, $content);

        $falseUserId = uniqid();

        $this->reportbookService->deleteComment($comment->id(), $falseUserId);
    }
}
