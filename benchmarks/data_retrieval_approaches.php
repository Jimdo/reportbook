<?php

namespace Jimdo\Reports;

/**
 * My base benchmark
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 */
class DataRetrievalApproachesBench
{
    private $reportAmount = 10000;

    public function benchDisk()
    {
        
    }

    public function setUp()
    {
        $this->createRandomReports('disk');
    }

    public function tearDown()
    {
        for ($i=0; $i < $this->reportAmount; $i++) {
            unlink(__DIR__ . '/FixtureReports/' . $i);
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
