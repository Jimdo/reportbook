<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;
use Jimdo\Reports\Reportbook\Report;

class ReportMySQLRepository
{
    /** @var PDO */
    private $dbHandler;

    /** @var Serializer */
    private $serializer;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /** @var string */
    private $table;

    /**
     * @param PDO $dbHandler
     * @param Serializer $serializer
     * @param ApplicationConfig $applicationConfig
     */
    public function __construct(\PDO $dbHandler, Serializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
        $this->serializer = $serializer;
        $this->dbHandler = $dbHandler;
        $this->table = 'report';
    }

    /**
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $calendarYear
     * @param string $category
     * @return Report
     */
    public function create(
        TraineeId $traineeId,
        string $content,
        string $date,
        string $calendarWeek,
        string $calendarYear,
        string $category
    ) {
        $report = new Report($traineeId, $content, $date, $calendarWeek, $calendarYear, uniqid(), $category);

        $this->save($report);

        return $report;
    }

    /**
     * @param string $id
     * @return Report
     */
    public function findById(string $id)
    {
        return $this->serializer->unserializeReport(
            $this->dbHandler->query(
                "SELECT * FROM {$this->table} WHERE id = '{$id}'"
            )->fetchAll()[0]
        );
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $reports = [];
        foreach ($this->dbHandler->query("SELECT * FROM {$this->table}")->fetchAll() as $pdoObject) {
            $reports[] = $this->serializer->unserializeReport($pdoObject);
        }
        return $reports;
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $this->dbHandler->exec("INSERT INTO {$this->table} (
            id, content, date, calendarWeek, calendarYear, status, category, traineeId
        ) VALUES (
            '{$report->id()}', '{$report->content()}', '{$report->date()}', '{$report->calendarWeek()}', '{$report->calendarYear()}', '{$report->status()}', '{$report->category()}', '{$report->traineeId()}'
        )");
    }
}
