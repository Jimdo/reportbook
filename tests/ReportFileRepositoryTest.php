<?php

namespace Jimdo\Reports;

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
        $traineeId = uniqid();
        $expectedContent = 'some content';

        $expectedReport = $repository->create($traineeId, $expectedContent);
        $reportId = $expectedReport->id();

        $reportFileName = sprintf('%s/%s/%s'
            , self::REPORTS_ROOT_PATH
            , $traineeId
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
        $traineeId = uniqid();
        $content = 'some content';

        $report = $repository->create($traineeId, $content);

        $reportFileName = sprintf('%s/%s/%s'
            , self::REPORTS_ROOT_PATH
            , $traineeId
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

        $this->assertCount(0, $repository->findAll());

        $reports = [];
        $reports[] = $repository->create(uniqid(), $content);
        $reports[] = $repository->create(uniqid(), $content);
        $reports[] = $repository->create(uniqid(), $content);

        $foundReports = $repository->findAll();

        $this->assertCount(3, $foundReports);
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
