<?php

namespace Jimdo\Reports;

/**
 * My base benchmark
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 */
class DataRetrievalApproachesBench
{
    private $reportAmount = 1000;

    /**
     * @Revs({10, 100, 1000, 10000})
     * @Iterations(5)
     */
    public function benchFetchByIdFromDisk()
    {
        $this->diskFindById($this->reportAmount - 1);
    }

    public function setUp()
    {
        $this->createRandomReports('disk');
    }

    public function tearDown()
    {
        $files = scandir(__DIR__ . '/FixtureReports/');
        foreach ($files as $filename) {
            if ($filename !== '.' && $filename !== '..') {
                unlink(__DIR__ . '/FixtureReports/' . $filename);
            }
        }
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function diskFindById(string $reportId): array
    {
        $reportsPath = __DIR__ . '/FixtureReports/';

        foreach (scandir($reportsPath) as $reports) {
            if ($reports === '.' || $reports === '..') {
                continue;
            }
            $serializedReport = file_get_contents($reportsPath . '/' . $reports);
            $unserializedReport = unserialize($serializedReport);
            if ($unserializedReport['id'] == $reportId) {
                return $unserializedReport;
            }
        }
    }

    /**
     * @param string $persistence
     */
    protected function createRandomReports(string $persistence)
    {
        $reports = [];
        for ($i=0; $i < $this->reportAmount; $i++) {
            $reports[] = [
                'traineeId' => uniqid(),
                'content' => 'bla',
                'date' => '11.11.11',
                'calendarWeek' => '11',
                'id' => $i
            ];
        }

        switch ($persistence) {
            case 'disk':
            foreach ($reports as $report) {
                file_put_contents(__DIR__ . '/FixtureReports/' . $report['id'], serialize($report));
            }
            break;
        }
    }
}
