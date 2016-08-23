<?php

namespace Jimdo\Reports;

class ReportFileRepository implements ReportRepository
{
    /** @var string */
    private $reportsPath;

    /**
     * @param string $reportsPath
     */
    public function __construct(string $reportsPath)
    {
         $this->reportsPath = $reportsPath;
    }

    /**
     * @return string
     */
    public function reportsPath(): string
    {
        return $this->reportsPath;
    }

    /**
     * @param string $traineeId
     * @param string $content
     * @return Report
     */
    public function create(string $traineeId, string $content): Report
    {
        $report = new Report($traineeId, $content);
        $this->save($report);

        return $report;
    }

    /**
     * @param Report $report
     */
    public function delete(Report $report)
    {
        unlink($this->filename($report));
    }

    /**
     * @param Report $report
     */
    public function save(Report $report)
    {
        $this->ensureReportsPath();
        $this->ensureTraineeReportsPath($report->traineeId());
        file_put_contents($this->filename($report), serialize($report));
    }

    /**
     * @return Report[]
     */
     public function findAll(): array
     {
         $foundReports = [];
         $files = scandir($this->reportsPath);
         foreach ($files as $traineeId) {
             if ($traineeId === '.' || $traineeId === '..' || $traineeId === '.DS_Store') {
                 continue;
             }
             $reports = scandir($this->reportsPath . '/' . $traineeId);
             foreach ($reports as $report) {
                 if ($report === '.' || $report === '..' || $report === '.DS_Store') {
                     continue;
                 }
                 $serializedReport = file_get_contents($this->reportsPath . '/' . $traineeId . '/' . $report);
                 $foundReports[] = unserialize($serializedReport);
             }
         }
         return $foundReports;
     }

    /**
     * @param string $traineeId
     * @return Report[]
     */
    public function findByTraineeId(string $traineeId): array
    {
        $foundReports = [];
        $traineePath = $this->reportsPath . '/' . $traineeId;
        $files = scandir($traineePath);
        foreach ($files as $reports) {
            if ($reports === '.' || $reports === '..') {
                continue;
            }
            $serializedReport = file_get_contents($traineePath . '/' . $reports);
            $foundReports[] = unserialize($serializedReport);
        }
        return $foundReports;
    }

    /**
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        $allReports = $this->findAll();
        $foundReports = [];

        foreach ($allReports as $report) {
            if ($report->status() === $status) {
                $foundReports[] = $report;
            }
        }
        return $foundReports;
    }

    /**
     * @param string $id
     * @return Report
     */
    public function findById(string $id)
    {
        $allReports = $this->findAll();

        foreach ($allReports as $report) {
            if ($report->id() === $id) {
                return $report;
            }
        }
    }

    private function ensureReportsPath()
    {
        if (!file_exists($this->reportsPath)) {
            mkdir($this->reportsPath);
        }
    }

    /**
     * @param string $traineeId
     */
    private function ensureTraineeReportsPath(string $traineeId)
    {
        $traineeReportsPath = $this->reportsPath . '/' . $traineeId;
        if (!file_exists($traineeReportsPath)) {
            mkdir($traineeReportsPath);
        }
    }

    /**
     * @param Report $report
     * @return string
     */
    private function filename(Report $report): string
    {
        return $filename = $this->reportsPath . '/' . $report->traineeId() . '/' . $report->id();
    }
}
