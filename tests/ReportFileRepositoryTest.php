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

        $this->assertEquals(self::REPORTS_ROOT_PATH, $repository->rootPath());
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $repository = new ReportFileRepository(self::REPORTS_ROOT_PATH);
        $traineeId = uniqid();
        $expectedContent = 'some content';

        $report = $repository->create($traineeId, $expectedContent);
        $reportId = $report->id();

        $reportFileName = sprintf('%s/%s/%s'
            , self::REPORTS_ROOT_PATH
            , $traineeId
            , $report->id()
        );

        $content = file_get_contents($reportFileName);

        $this->assertTrue($content !== false);
        $this->assertEquals($expectedContent, $content);
    }

    private function removeAllReports()
    {
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
