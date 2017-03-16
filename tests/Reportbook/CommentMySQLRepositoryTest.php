<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\Serializer;
use Jimdo\Reports\Web\ApplicationConfig;

class CommentMySQLRepositoryTest extends TestCase
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

    /** @var reportId */
    private $reportId;

    /** @var Serializer */
    private $serializer;

    protected function setUp()
    {
        $appConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');

        $this->database = $appConfig->mysqlDatabase;
        $this->table = 'comment';

        $uri = "mysql:host={$appConfig->mysqlHost};dbname={$this->database}";

        $this->dbHandler = new \PDO($uri, $appConfig->mysqlUser, $appConfig->mysqlPassword);

        $this->serializer = new Serializer();
        $this->repository = new CommentMySQLRepository($this->dbHandler, $this->serializer, $appConfig);

        $this->userId = uniqId();
        $this->dbHandler->exec("INSERT INTO user (
            id, username, email, password, roleName, roleStatus
        ) VALUES (
            '{$this->userId}', 'testuser', 'testemail', 'geheim', 'TRAINEE', 'APPROVED'
        )");

        $this->reportId = uniqId();
        $this->dbHandler->exec("INSERT INTO report (
            id, content, date, calendarWeek, calendarYear, status, category, traineeId
        ) VALUES (
            '{$this->reportId}', 'some content', '10.10.10', '30', '2017', 'APPROVAL_REQUESTED', 'SCHOOL', '{$this->userId}'
        )");
    }

    protected function tearDown()
    {
        $this->dbHandler->exec("DELETE FROM comment");
        $this->dbHandler->exec("DELETE FROM report");
        $this->dbHandler->exec("DELETE FROM user");
    }
}
