<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Views\Report;
use function GuzzleHttp\json_encode;

class Serializer {
    public function serializeReport(Report $report) {
        return json_encode([
            'id' => $report->id(),
            'username' => 'Klaus',
            'calendarWeek' => $report->calendarWeek(),
            'calendarYear' => $report->calendarYear(),
            'category' => $report->category(),
            'content' => $report->content()
        ]);
    }
    public function serializeReports($reports) {
        $serializedReports = [];

        foreach ($reports as $report) {
            $serializedReport = [
                'id' => $report->id(),
                'content' => $report->content(),
                'traineeId' => $report->traineeId(),
                'status' => $report->status(),
                'date' => $report->date(),
                'calendarWeek' => $report->calendarWeek(),
                'calendarYear' => $report->calendarYear(),
                'category' => $report->category()
            ];
            $serializedReports[] = $serializedReport;
        }
        return json_encode($serializedReports);
    }
}