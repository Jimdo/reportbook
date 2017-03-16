<?php

namespace Jimdo\Reports\Reportbook;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\MySQLSerializer;
use Jimdo\Reports\Reportbook\Report;

class ReportMySQLRepository implements ReportRepository
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
    public function __construct(\PDO $dbHandler, MySQLSerializer $serializer, ApplicationConfig $applicationConfig)
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
    ): Report {
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
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$id]);

        return $this->serializer->unserializeReport($sth->fetchAll()[0]);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $reports = [];
        foreach ($this->dbHandler->query(
                    "SELECT * FROM {$this->table} ORDER BY calendarYear DESC, calendarWeek DESC"
                )->fetchAll() as $pdoObject) {
            $reports[] = $this->serializer->unserializeReport($pdoObject);
        }
        return $reports;
    }

    /**
     * @param string $traineeId
     * @return array
     */
    public function findByTraineeId(string $traineeId): array
    {
        $sql = "SELECT * FROM $this->table WHERE traineeId = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$traineeId]);

        $reports = [];
        foreach ($sth->fetchAll() as $pdoObject) {
            $reports[] = $this->serializer->unserializeReport($pdoObject);
        }
        return $reports;
    }

    /**
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        $sql = "SELECT * FROM $this->table WHERE status = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$status]);

        $reports = [];
        foreach ($sth->fetchAll() as $pdoObject) {
            $reports[] = $this->serializer->unserializeReport($pdoObject);
        }
        return $reports;
    }

    /**
     * @param string $text
     * @return array
     */
    public function findReportsByString(string $text): array
    {
        if ($text === '') {
            return $this->findAll();
        }

        $reports = [];
        if (is_numeric($text)) {
            $sql = "SELECT * FROM $this->table WHERE calendarWeek = ?";
            $sth = $this->dbHandler->prepare($sql);
            $sth->execute([$text]);

            foreach ($sth->fetchAll() as $pdoObject) {
                $reports[] = $this->serializer->unserializeReport($pdoObject);
            }

        } else {
            $sql = "SELECT * FROM $this->table WHERE content LIKE ?";
            $sth = $this->dbHandler->prepare($sql);
            $sth->execute(["%{$text}%"]);

            foreach ($sth->fetchAll() as $pdoObject) {
                $reports[] = $this->serializer->unserializeReport($pdoObject);
            }
        }
        return $reports;
    }

    /**
     * @param Report $report
     */
    public function delete(Report $report)
    {
        $sql = "DELETE FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$report->id()]);
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $sql = "INSERT INTO $this->table (
            id, content, date, calendarWeek, calendarYear, status, category, traineeId
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?
        )";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([
            $report->id(),
            $report->content(),
            $report->date(),
            $report->calendarWeek(),
            $report->calendarYear(),
            $report->status(),
            $report->category(),
            $report->traineeId()
        ]);
    }
}
