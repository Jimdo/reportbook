<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;

class ReportFileRepositoryTest extends TestCase
{
    const REPORTS_ROOT_PATH = 'tests/reports';

    protected function setUp()
    {
        $this->deleteRecursive(self::REPORTS_ROOT_PATH);
    }

    protected function tearDown()
    {
        $this->deleteRecursive(self::REPORTS_ROOT_PATH);
    }

    /**
     * @test
     */
    public function itShouldHaveRepositoryRoot()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);

        $this->assertEquals(self::REPORTS_ROOT_PATH, $repository->reportsPath());
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);
        $traineeId = new TraineeId();
        $category = new Category(Category::SCHOOL);
        $expectedContent = 'some content';

        $expectedReport = $repository->create($traineeId, $expectedContent, '10.10.10', '34', $category);
        $reportId = $expectedReport->id();

        $reportFileName = sprintf('%s/%s/%s'
            , self::REPORTS_ROOT_PATH
            , $traineeId->id()
            , $expectedReport->id()
        );

        $report = unserialize(file_get_contents($reportFileName));

        $this->assertEquals($expectedReport->content(), $report->content());
    }

    /**
    * @test
    */
    public function itShouldDeleteReport()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);
        $traineeId = new TraineeId();
        $category = new Category(Category::SCHOOL);
        $content = 'some content';

        $report = $repository->create($traineeId, $content, '10.10.10', '34', $category);

        $reportFileName = sprintf('%s/%s/%s'
            , self::REPORTS_ROOT_PATH
            , $traineeId->id()
            , $report->id()
        );

        $this->assertTrue(file_exists($reportFileName));

        $repository->delete($report);

        $this->assertFalse(file_exists($reportFileName));
    }

    /**
     * @test
     */
    public function itShouldFindAllReports()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);
        $content = 'some content';
        $category = new Category(Category::SCHOOL);

        $this->assertCount(0, $repository->findAll());

        $reports = [];
        $reports[] = $repository->create(new TraineeId(), $content, '10.10.10', '34', $category);
        $reports[] = $repository->create(new TraineeId(), $content, '10.10.10', '34', $category);
        $reports[] = $repository->create(new TraineeId(), $content, '10.10.10', '34', $category);

        $foundReports = $repository->findAll();

        $this->assertCount(3, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindByTraineeId()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);
        $category = new Category(Category::SCHOOL);
        $content = 'some content';

        $traineeId1 = new TraineeId();
        $traineeId2 = new TraineeId();

        $reports = [];
        $reports[] = $repository->create($traineeId1, $content, '10.10.10', '34', $category);
        $reports[] = $repository->create($traineeId1, $content, '10.10.10', '34', $category);
        $reports[] = $repository->create($traineeId2, $content, '10.10.10', '34', $category);
        $reports[] = $repository->create($traineeId2, $content, '10.10.10', '34', $category);

        $foundReports = $repository->findByTraineeId($traineeId1->id());
        $this->assertCount(2, $foundReports);

        $foundReports = $repository->findByTraineeId($traineeId2->id());
        $this->assertCount(2, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindByStatus()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);
        $category = new Category(Category::SCHOOL);
        $content = 'some content';

        $reports = [];
        $reports[] = $repository->create(new TraineeId(), $content, '10.10.10', '34', $category);
        $reports[] = $repository->create(new TraineeId(), $content, '10.10.10', '34', $category);

        $foundReports = $repository->findByStatus(Report::STATUS_NEW);

        $this->assertEquals(Report::STATUS_NEW, $foundReports[0]->status());
        $this->assertEquals(Report::STATUS_NEW, $foundReports[1]->status());
    }

    /**
     * @test
     */
    public function itShouldFindById()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);
        $category = new Category(Category::SCHOOL);
        $content = 'some content';

        $report1 = $repository->create(new TraineeId(), $content, '10.10.10', '34', $category);
        $report2 = $repository->create(new TraineeId(), $content, '10.10.10', '34', $category);

        $foundReport = $repository->findById($report1->id());
        $this->assertEquals($report1, $foundReport);

        $foundReport = $repository->findById($report2->id());
        $this->assertEquals($report2, $foundReport);
    }

    /**
     * @test
     */
    public function itShouldReturnAnEmptyListIfUserNotExists()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);
        $traineeId = new TraineeId();

        $reports = $repository->findByTraineeId($traineeId->id());

        $this->assertEquals([], $reports);
    }

    private function deleteRecursive($input)
    {
        if (is_file($input)) {
            unlink($input);
            return;
        }

        if (is_dir($input)) {
            foreach (scandir($input) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $file = join('/', [$input, $file]);
                if (is_file($file)) {
                    unlink($file);
                    continue;
                }

                $this->deleteRecursive($file);

                rmdir($file);
            }
        }
    }
}
