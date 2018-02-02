<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Views\Report;
use function GuzzleHttp\json_encode;
use Jimdo\Reports\Profile\Profile;
use Jimdo\Reports\User\User;

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

    public function serializeProfile(Profile $profile, User $user)
    {
        return json_encode([
            'forename' => $profile->forename(),
            'surname' => $profile->surname(),
            'username' => $user->username(),
            'email' => $user->email(),
            'dateOfBirth' => $profile->dateOfBirth(),
            'company' => $profile->company(),
            'jobTitle' => $profile->jobTitle(),
            'school' => $profile->school(),
            'grade' => $profile->grade(),
            'trainingYear' => $profile->trainingYear(),
            'startOfTraining' => $profile->startOfTraining()
        ]);
    }
}
