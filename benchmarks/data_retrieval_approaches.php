<?php

namespace Jimdo\Reports;

use Jimdo\Reports\Web\ApplicationConfig;

/**
 * My base benchmark
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 */
class DataRetrievalApproachesBench
{
    /** @var integer */
    private $reportAmount = 1000;

    /** @var Client $client */
    private $client;

    /** @var Collection $reports */
    private $reports;

    /** @var ApplicationConfig */
    private $appConfig;

    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchFetchByIdFromDisk()
    {
        $this->diskFindById($this->reportAmount - 1);
    }

    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchFetchByIdFromMongoDB()
    {
        $this->mongoFindById($this->reportAmount - 1);
    }

    public function setUp()
    {
        $this->createRandomReports('disk');
        $this->createRandomReports('mongoDB');
    }

    public function tearDown()
    {
        $files = scandir(__DIR__ . '/FixtureReports/');
        foreach ($files as $filename) {
            if ($filename !== '.' && $filename !== '..') {
                unlink(__DIR__ . '/FixtureReports/' . $filename);
            }
        }

        $this->reports->deleteMany([]);
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function mongoFindById(string $reportId): array
    {
        foreach ($this->reports->find() as $report) {
            $report = $report->getArrayCopy();

            if ($report['id'] == $reportId) {
                return $report;
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

            case 'mongoDB':
            $this->appConfig = new ApplicationConfig(__DIR__ . '/../config.yml');

            $uri = sprintf('mongodb://%s:%s@%s:%d/%s'
                , $this->appConfig->mongoUsername
                , $this->appConfig->mongoPassword
                , $this->appConfig->mongoHost
                , $this->appConfig->mongoPort
                , $this->appConfig->mongoDatabase
            );
            $this->client = new \MongoDB\Client($uri);
            $reportbook = $this->client->selectDatabase($this->appConfig->mongoDatabase);
            $this->reports = $reportbook->reports;

            foreach ($reports as $report) {
                $this->reports->insertOne($report);
            }
            break;
        }
    }
}
