<?php

namespace Jimdo\Reports;

class ReportFileRepository implements ReportRepository
{
    /** @var string */
    private $rootPath;

    /**
     * @param string $rootPath
     */
    public function __construct(string $rootPath)
    {
         $this->rootPath = $rootPath;
    }

    /**
     * @return string
     */
    public function rootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * @param string $traineeId
     * @param string $content
     * @return Report
     */
    public function create(string $traineeId, string $content): Report
    {
        $report = new Report($traineeId, $content);
        $reportId = $report->id();
        $reportsPath = "{$this->rootPath}/{$traineeId}";
        $filename = "{$reportsPath}/{$reportId}";

        mkdir($reportsPath);
        file_put_contents($filename, $content);

        return $report;
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {

    }

    /**
     * @return Report[]
     */
    public function findAll(): array
    {

    }

    /**
     * @param string $traineeId
     * @return Report[]
     */
    public function findByTraineeId(string $traineeId): array
    {

    }

    /**
     * @param Report $report
     */
    public function delete(Report $report)
    {

    }

    /**
     * @param string $status
     * @return Report[]
     */
    public function findByStatus(string $status): array
    {

    }

    /**
     * @param string $id
     * @return Report
     */
    public function findById(string $id): Report
    {

    }
}
