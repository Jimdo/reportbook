<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Views\Report;
use Jimdo\Reports\Notification\BrowserNotification;

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

    public function serializeNotifications($notifications) {
        $serializedNotifications = [];

        foreach ($notifications as $notification) {
            $serializedNotification = [
                'id' => $notification->id(),
                'title' => $notification->title(),
                'description' => $notification->description(),
                'userId' => $notification->userId(),
                'reportId' => $notification->reportId(),
                'status' => $notification->status(),
                'time' => $notification->time()
            ];
            $serializedNotifications[] = $serializedNotification;
        }
        return json_encode($serializedNotifications);
    }
}
