<?php

namespace Jimdo\Reports;

interface ReportRepository
{
    /**
     * @param string $traineeId
     * @param string $content
     * @return Report
     */
    public function create(string $traineeId, string $content): Report;

    /**
     * @param Report $report
     */
    public function save(Report $report);

    /**
     * @return Report[]
     */
    public function findAll(): array;

    /**
     * @param string $traineeId
     * @return Report[]
     */
    public function findByTraineeId(string $traineeId): array;

    /**
     * @param Report $report
     */
    public function delete(Report $report);

    /**
     * @param string $status
     * @return Report[]
     */
    public function findByStatus(string $status): array;

    /**
     * @param string $id
     * @return Report
     */
    public function findById(string $id): Report;
}
