<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Serializer as Serializer;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

class ReportMySQLRepositoryTest extends TestCase
{
    /** @var PDO */
    private $dbHandler;

    /** @var ReportMySQLRepository */
    private $repository;

    /** @var MySQL Database */
    private $database;

    /** @var MySQL Table */
    private $table;

    /** @var userId */
    private $userId;

    /** @var Serializer */
    private $serializer;

    protected function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

        $this->database = $appConfig->mysqlDatabase;
        $this->table = 'report';

        $uri = "mysql:host={$appConfig->mysqlHost};dbname={$this->database}";

        $this->dbHandler = new \PDO($uri, $appConfig->mysqlUser, $appConfig->mysqlPassword);

        $this->serializer = new Serializer();
        $this->repository = new ReportMySQLRepository($this->dbHandler, $this->serializer, $appConfig);

        $this->dbHandler->exec("DELETE FROM report");
        $this->dbHandler->exec("DELETE FROM user");

        $this->userId = uniqId();
        $this->dbHandler->exec("INSERT INTO user (
            id, username, email, password, role, roleStatus
        ) VALUES (
            '{$this->userId}', 'testuser', 'testemail', 'geheim', 'TRAINEE', 'APPROVED'
        )");
    }

    /**
     * @test
     */
    public function itShouldCreateReport()
    {
        $traineeId = new TraineeId($this->userId);
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';
        $calendarYear = '2017';
        $category = Category::SCHOOL;

        $report = $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);

        $query = $this->dbHandler->query("SELECT * FROM {$this->table} WHERE id = '{$report->id()}'");
        $foundReport = $this->serializer->unserializeReport($query->fetchAll()[0]);

        $this->assertEquals($report->id(), $foundReport->id());
    }

    /**
     * @test
     */
    public function itShouldFindOneReportById()
    {
        $traineeId = new TraineeId($this->userId);
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';
        $calendarYear = '2017';
        $category = Category::SCHOOL;

        $report = $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);

        echo 'Find Report via MYSQL' . PHP_EOL;
        var_dump($this->dbHandler->query("SELECT * FROM report WHERE id = '{$report->id()}'")->fetchAll());

        var_dump($this->dbHandler->query("SHOW DATABASES;")->fetchAll());
        var_dump($this->dbHandler->query("USE reportbook_test;")->fetchAll());
        var_dump($this->dbHandler->query("SHOW TABLES;")->fetchAll());

        $foundReport = $this->repository->findById($report->id());

        $this->assertEquals($report->id(), $foundReport->id());
    }
}
