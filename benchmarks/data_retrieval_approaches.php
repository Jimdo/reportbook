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
    private $reportAmount = 10;

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
        $this->diskFindById($this->reportAmount);
    }

    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchFetchByDateFromDisk()
    {
        $this->diskFindByDate('11.11.11');
    }

    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchFetchByTextFromDisk()
    {
        $this->diskFindByText('text:' . $this->reportAmount);
    }

    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchFetchByIdFromMongoDB()
    {
        $this->mongoFindById($this->reportAmount);
    }

    /**
    * @Revs({10, 100, 1000})
    * @Iterations(5)
    */
    public function benchFetchByDateFromMongoDB()
    {
        $this->mongoFindByDate('11.11.11');
    }

    /**
    * @Revs({10, 100, 1000})
    * @Iterations(5)
    */
    public function benchFetchByTextFromMongoDB()
    {
        $this->mongoFindByText('text:' . $this->reportAmount);
    }

    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchFetchByIdFromMongoDBWithQuery()
    {
        $this->mongoFindByIdWithQuery($this->reportAmount);
    }

    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchFetchByDateFromMongoDBWithQuery()
    {
        $this->mongoFindByDateWithQuery('11.11.11');
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
    public function mongoFindById(int $reportId): array
    {
        foreach ($this->reports->find() as $report) {
            $report = $report->getArrayCopy();

            if ($report['id'] === $reportId) {
                return $report;
            }
        }
    }

    /**
    * @param string $date
    * @return array
    */
    public function mongoFindByDate(string $date): array
    {
        foreach ($this->reports->find() as $report) {
            $report = $report->getArrayCopy();

            if ($report['date'] === $date) {
                $foundReports[] = $report;
            }
        }
        return $foundReports;
    }

    /**
    * @param string $text
    * @return array
    */
    public function mongoFindByText(string $text): array
    {
        foreach ($this->reports->find() as $report) {
            $report = $report->getArrayCopy();

            if ($report['content'] === $text) {
                $foundReports[] = $report;
            }
        }
        return $foundReports;
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function mongoFindByIdWithQuery(int $reportId): array
    {
        return $this->reports->findOne(['id' => $reportId])->getArrayCopy();
    }

    /**
     * @param string $date
     * @return array
     */
    public function mongoFindByDateWithQuery(string $date): array
    {
        return $this->reports->findOne(['date' => $date])->getArrayCopy();
    }

    /**
     * @param string $reportId
     * @return array
     */
    public function diskFindById(int $reportId): array
    {
        $reportsPath = __DIR__ . '/FixtureReports/';

        foreach (scandir($reportsPath) as $reports) {
            if ($reports === '.' || $reports === '..') {
                continue;
            }
            $serializedReport = file_get_contents($reportsPath . '/' . $reports);
            $unserializedReport = unserialize($serializedReport);
            if ($unserializedReport['id'] === $reportId) {
                return $unserializedReport;
            }
        }
    }

    /**
     * @param string $date
     * @return array
     */
    public function diskFindByDate(string $date): array
    {
        $reportsPath = __DIR__ . '/FixtureReports/';

        foreach (scandir($reportsPath) as $reports) {
            if ($reports === '.' || $reports === '..') {
                continue;
            }
            $serializedReport = file_get_contents($reportsPath . '/' . $reports);
            $unserializedReport = unserialize($serializedReport);
            if ($unserializedReport['date'] === $date) {
                $foundReports[] = $unserializedReport;
            }
        }
        return $foundReports;
    }

    /**
     * @param string $text
     * @return array
     */
    public function diskFindByText(string $text)
    {
        $reportsPath = __DIR__ . '/FixtureReports/';

        foreach (scandir($reportsPath) as $reports) {
            if ($reports === '.' || $reports === '..') {
                continue;
            }
            $serializedReport = file_get_contents($reportsPath . '/' . $reports);
            $unserializedReport = unserialize($serializedReport);
            if ($unserializedReport['content'] === $text) {
                $foundReports[] = $unserializedReport;
            }
        }
        return $foundReports;

    }

    /**
     * @param string $persistence
     */
    protected function createRandomReports(string $persistence)
    {
        $reports = [];
        for ($i=1; $i <= $this->reportAmount; $i++) {
            $reports[] = [
                'traineeId' => uniqid(),
                'content' => 'text:' . $i,
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
