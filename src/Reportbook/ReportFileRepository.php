<?php

namespace Jimdo\Reports\Reportbook;

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
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $category
     * @return Report
     * @throws ReportFileRepositoryException
     */
    public function create(TraineeId $traineeId, string $content, string $date, string $calendarWeek, string $category): Report
    {
        $report = new Report($traineeId, $content, $date, $calendarWeek, uniqid(), $category);
        $this->save($report);

        return $report;
    }

    /**
     * @param Report $report
     * @throws ReportFileRepositoryException
     */
    public function delete(Report $report)
    {
        $filename = $this->filename($report);
        if (!unlink($filename)) {
            throw new ReportFileRepositoryException("Could not delete $filename!");
        }
    }

    /**
     * @param Report $report
     * @throws ReportFileRepositoryException
     */
    public function save(Report $report)
    {
        $this->ensureReportsPath();
        $this->ensureTraineeReportsPath($report->traineeId());
        $filename = $this->filename($report);
        if (file_put_contents($filename, serialize($report)) === false) {
            throw new ReportFileRepositoryException("Could not write to $filename!");
        }
    }

    /**
     * @return Report[]
     * @throws ReportFileRepositoryException
     */
    public function findAll(): array
    {
        $foundReports = [];
        $this->ensureReportsPath();
        foreach ($this->readDirectory($this->reportsPath) as $traineeId) {
            if ($traineeId === '.' || $traineeId === '..' || $traineeId === '.DS_Store') {
                continue;
            }
            foreach ($this->readDirectory($this->reportsPath . '/' . $traineeId) as $report) {
                if ($report === '.' || $report === '..' || $report === '.DS_Store') {
                    continue;
                }
                $serializedReport = @file_get_contents($this->reportsPath . '/' . $traineeId . '/' . $report);
                if ($serializedReport === false) {
                    throw new ReportFileRepositoryException(
                        'Could not read file: ' . $this->reportsPath . '/' . $traineeId . '/' . $report
                    );
                }
                $unserializedReport = @unserialize($serializedReport);
                if ($unserializedReport === false) {
                    throw new ReportFileRepositoryException('Could not unserialize report!');
                }
                $foundReports[] = $unserializedReport;
            }
        }
        return $foundReports;
    }

    /**
     * @param string $traineeId
     * @return Report[]
     * @throws ReportFileRepositoryException
     */
    public function findByTraineeId(string $traineeId): array
    {
        $foundReports = [];
        $this->ensureReportsPath();
        $this->ensureTraineeReportsPath($traineeId);
        $traineePath = $this->reportsPath . '/' . $traineeId;

        foreach ($this->readDirectory($traineePath) as $reports) {
            if ($reports === '.' || $reports === '..') {
                continue;
            }
            $serializedReport = @file_get_contents($traineePath . '/' . $reports);
            if ($serializedReport === false) {
                throw new ReportFileRepositoryException('Could not read file: ' . $traineePath . '/' . $reports);
            }
            $unserializedReport = @unserialize($serializedReport);
            if ($unserializedReport === false) {
                throw new ReportFileRepositoryException('Could not unserialize report!');
            }
            $foundReports[] = $unserializedReport;
        }
        return $foundReports;
    }

    /**
     * @param string $status
     * @return array
     * @throws ReportFileRepositoryException
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
     * @param string $text
     * @return Report[]
     */
    public function findReportsByString(string $text): array
    {
        $foundReports = $this->findAll();
        $reports = [];

        foreach ($foundReports as $report) {
            if (strpos($report->content(), $text) !== false) {
                $reports[] = $report;
            }
        }
        return $reports;
    }

    /**
     * @param string $id
     * @return Report
     * @throws ReportFileRepositoryException
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

    /**
     * @throws ReportFileRepositoryException
     */
    private function ensureReportsPath()
    {
        if (!file_exists($this->reportsPath)) {
            if (!mkdir($this->reportsPath)) {
                throw new ReportFileRepositoryException("Could not create directory: $this->reportsPath");
            }
        }
    }

    /**
     * @param string $traineeId
     * @throws ReportFileRepositoryException
     */
    private function ensureTraineeReportsPath(string $traineeId)
    {
        $traineeReportsPath = $this->reportsPath . '/' . $traineeId;
        if (!file_exists($traineeReportsPath)) {
            if (!mkdir($traineeReportsPath)) {
                throw new ReportFileRepositoryException("Could not create directory: $traineeReportsPath");
            }
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

    /**
     * @param string $path
     * @return array
     * @throws ReportFileRepositoryException
     */
    private function readDirectory(string $path): array
    {
        $files = @scandir($path);
        if ($files === false) {
            throw new ReportFileRepositoryException("Could not read directory: $path");
        }
        return $files;
    }
}
