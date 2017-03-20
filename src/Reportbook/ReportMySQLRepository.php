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

        $report = $sth->fetchAll();
        if (array_key_exists('0', $report)) {
            return $this->serializer->unserializeReport($report[0]);
        }
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
        $sql = "SELECT * FROM $this->table WHERE traineeId = ? ORDER BY calendarYear DESC, calendarWeek DESC";
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
        $sql = "SELECT * FROM $this->table WHERE status = ? ORDER BY calendarYear DESC, calendarWeek DESC";
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

        $sql = "SELECT * FROM $this->table WHERE calendarWeek = ? OR content LIKE ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([
            $text,
            "%{$text}%"
        ]);

        foreach ($sth->fetchAll() as $pdoObject) {
            $reports[] = $this->serializer->unserializeReport($pdoObject);
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
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$report->id()]);
        $foundReport = $sth->fetchAll();

        if (array_key_exists('0', $foundReport)) {
            $sql = "UPDATE $this->table SET id=:id, content=:content, date=:date, calendarWeek=:calendarWeek,
                    calendarYear=:calendarYear, status=:status, category=:category, traineeId=:traineeId WHERE id = :id";
        } else {
            $sql = "INSERT INTO $this->table (
                id, content, date, calendarWeek, calendarYear, status, category, traineeId
            ) VALUES (
                :id, :content, :date, :calendarWeek, :calendarYear, :status, :category, :traineeId
            )";
        }

        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([
            ':id' => $report->id(),
            ':content' => $report->content(),
            ':date' => $report->date(),
            ':calendarWeek' => $report->calendarWeek(),
            ':calendarYear' => $report->calendarYear(),
            ':status' => $report->status(),
            ':category' => $report->category(),
            ':traineeId' => $report->traineeId()
        ]);
    }
}
