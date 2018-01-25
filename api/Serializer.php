<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Views\Report;

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
}
