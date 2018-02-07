<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jimdo\Reports\Views\Report;
use Jimdo\Reports\Notification\BrowserNotification;

use function GuzzleHttp\json_encode;
use Jimdo\Reports\Profile\Profile;
use Jimdo\Reports\User\User;
use Jimdo\Reports\Reportbook\Comment;

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
            'dateOfBirth' => date("d.m.Y", strtotime($profile->dateOfBirth())),
            'company' => $profile->company(),
            'jobTitle' => $profile->jobTitle(),
            'school' => $profile->school(),
            'grade' => $profile->grade(),
            'trainingYear' => $profile->trainingYear(),
            'startOfTraining' => date("d.m.Y", strtotime($profile->startOfTraining()))
        ]);
    }

    public function serializeNotifications(array $notifications) {
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

    public function serializeComments(array $comments) {
        $serializedComments = [];

        foreach ($comments as $comment) {
            $serializedComment = [
                'id' => $comment['comment']->id(),
                'reportId' => $comment['comment']->reportId(),
                'userId' => $comment['comment']->userId(),
                'date' => date("d.m.Y", strtotime($comment['comment']->date())),
                'content' => $comment['comment']->content(),
                'status' => $comment['comment']->status(),
                'username' => $comment['username']
            ];
            $serializedComments[] = $serializedComment;
        }
        return json_encode($serializedComments);
    }

    public function serializeComment(Comment $comment, User $user) {
        return json_encode(
            [
                'id' => $comment->id(),
                'reportId' => $comment->reportId(),
                'userId' => $comment->userId(),
                'date' => date("d.m.Y", strtotime($comment->date())),
                'content' => $comment->content(),
                'status' => $comment->status(),
                'username' => $user->username()
            ]
        );
    }
}
