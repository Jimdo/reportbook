<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class ReportFileRepositoryTest extends TestCase
{
    const REPORTS_ROOT_PATH = 'tests/reports';

    protected function setUp()
    {
        $this->removeAllReports();
    }

    protected function tearDown()
    {
        $this->removeAllReports();
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

    private function removeAllReports()
    {
        if (!file_exists(self::REPORTS_ROOT_PATH)) {
            return;
        }

        $files = scandir(self::REPORTS_ROOT_PATH);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $dir = self::REPORTS_ROOT_PATH . '/' . $file;
            if (is_dir($dir)) {
                $dirFiles = scandir($dir);
                foreach ($dirFiles as $file2) {
                    if ($file2 === '.' || $file2 === '..') {
                        continue;
                    }

                    $rmFile = $dir . '/' . $file2;
                    unlink($rmFile);
                }
                rmdir($dir);
            }
        }
    }
}
