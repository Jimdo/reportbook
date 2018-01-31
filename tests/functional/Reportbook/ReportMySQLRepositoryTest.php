<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\MySQLSerializer;
use Jimdo\Reports\Web\ApplicationConfig;

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

    /** @var MySQLSerializer */
    private $serializer;

    protected function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../../config.yml');

        $this->database = $appConfig->mysqlDatabase;
        $this->table = 'report';

        $uri = "mysql:host={$appConfig->mysqlHost};dbname={$this->database}";

        $this->dbHandler = new \PDO($uri, $appConfig->mysqlUser, $appConfig->mysqlPassword);

        $this->serializer = new MySQLSerializer();
        $this->repository = new ReportMySQLRepository($this->dbHandler, $this->serializer, $appConfig);

        $this->dbHandler->exec("DELETE FROM report");
        $this->dbHandler->exec("DELETE FROM user");

        $this->userId = uniqId();
        $this->dbHandler->exec("INSERT INTO user (
            id, username, email, password, roleName, roleStatus
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

        $foundReport = $this->repository->findById($report->id());

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

        $foundReport = $this->repository->findById($report->id());

        $this->assertEquals($report->id(), $foundReport->id());
    }

    /**
     * @test
     */
    public function itShouldFindReports()
    {
        $traineeId = new TraineeId($this->userId);
        $content = 'some content';
        $date = '10.10.10';
        $category = Category::SCHOOL;

        $report1 = $this->repository->create($traineeId, $content, $date, '30' , '2016', $category);
        $report2 = $this->repository->create($traineeId, $content, $date, '10' , '2018', $category);
        $report3 = $this->repository->create($traineeId, $content, $date, '20' , '2017', $category);

        $foundReports = $this->repository->findAll();

        $this->assertEquals($report2->calendarWeek(), $foundReports[0]->calendarWeek());
        $this->assertEquals($report3->calendarWeek(), $foundReports[1]->calendarWeek());
        $this->assertEquals($report1->calendarWeek(), $foundReports[2]->calendarWeek());
        $this->assertCount(3, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindReportsByTraineeId()
    {
        $traineeId = new TraineeId($this->userId);
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';
        $calendarYear = '2017';
        $category = Category::SCHOOL;

        $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);
        $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);
        $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);
        $this->repository->create(new TraineeId(), $content, $date, $calendarWeek , $calendarYear, $category);

        $foundReports = $this->repository->findByTraineeId($traineeId->id());

        $this->assertCount(3, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindReportsByStatus()
    {
        $traineeId = new TraineeId($this->userId);
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';
        $calendarYear = '2017';
        $category = Category::SCHOOL;

        $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);
        $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);
        $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);

        $foundReports = $this->repository->findByStatus(Report::STATUS_NEW);

        $this->assertCount(3, $foundReports);
    }

    /**
     * @test
     */
    public function itShouldFindReportsByString()
    {
        $traineeId = new TraineeId($this->userId);
        $date = '10.10.10';
        $calendarWeek = '34';
        $calendarYear = '2017';
        $category = Category::SCHOOL;

        $this->repository->create($traineeId, 'hello', $date, $calendarWeek , $calendarYear, $category);
        $this->repository->create($traineeId, 'you', $date, $calendarWeek , $calendarYear, $category);
        $report = $this->repository->create($traineeId, 'you hello', $date, $calendarWeek , $calendarYear, $category);

        $foundReport = $this->repository->findReportsByString('you hello');
        $foundReports = $this->repository->findReportsByString('hello');
        $foundAllReports = $this->repository->findReportsByString('');

        $this->assertEquals($report->id(), $foundReport[0]->id());
        $this->assertCount(2, $foundReports);
        $this->assertCount(3, $foundAllReports);
    }

    /**
     * @test
     */
    public function itShouldDeleteReport()
    {
        $traineeId = new TraineeId($this->userId);
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '34';
        $calendarYear = '2017';
        $category = Category::SCHOOL;

        $report = $this->repository->create($traineeId, $content, $date, $calendarWeek , $calendarYear, $category);

        $this->assertCount(1, $this->repository->findAll());

        $this->repository->delete($report);

        $this->assertCount(0, $this->repository->findAll());
    }
}
