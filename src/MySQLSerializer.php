<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\User;
use Jimdo\Reports\Reportbook\Comment;
use Jimdo\Reports\Profile\Profile;
use Jimdo\Reports\Reportbook\Report;

class MySQLSerializer implements Serializer {

    /**
     * @param User $user
     * @return array
     */
    public function serializeUser(User $user) : array
    {
        return [
            'id' => $user->id(),
            'username' => $user->username(),
            'email' => $user->email(),
            'role' => [
                'roleName' => $user->roleName(),
                'roleStatus' => $user->roleStatus()
            ],
            'password' => $user->password()
        ];
    }

    /**
     * @param array $serializedUser
     * @return User
     */
    public function unserializeUser(array $serializedUser) : User
    {
        $roleName = strtoupper($serializedUser['roleName']);
        $roleStatus = $serializedUser['roleStatus'];

        switch ($roleName) {
            case Role::TRAINEE:
                $role = new Role(Role::TRAINEE);
                break;
            case Role::TRAINER:
                $role = new Role(Role::TRAINER);
                break;
            case Role::ADMIN:
                $role = new Role(Role::ADMIN);
                break;
        }

        if ($roleStatus === Role::STATUS_APPROVED) {
            $role->approve();
        }

        return new User(
            $serializedUser['username'],
            $serializedUser['email'],
            $role,
            $serializedUser['password'],
            new UserId($serializedUser['id'])
        );
    }

    /**
     * @param User $user
     * @return array
     */
    public function serializeProfile(Profile $profile) : array
    {
        return [
            'userId' => $profile->userId(),
            'forename' => $profile->forename(),
            'surname' => $profile->surname(),
            'dateOfBirth' => $profile->dateOfBirth(),
            'company' => $profile->company(),
            'jobTitle' => $profile->jobTitle(),
            'school' => $profile->school(),
            'grade' => $profile->grade(),
            'trainingYear' => $profile->trainingYear(),
            'startOfTraining' => $profile->startOfTraining(),
            'image' => [
                'base64' => $profile->image(),
                'type' => $profile->imageType()
            ]
        ];
    }

    /**
     * @param array $serializedProfile
     * @return Profile
     */
    public function unserializeProfile(array $serializedProfile) : Profile
    {
        $profile = new Profile(
            $serializedProfile['userId'],
            $serializedProfile['forename'],
            $serializedProfile['surname']
        );

        $image = $serializedProfile['image'];
        $imageType = $serializedProfile['imageType'];

        $profile->editCompany($serializedProfile['company']);
        $profile->editSchool($serializedProfile['school']);
        $profile->editGrade($serializedProfile['grade']);
        $profile->editJobTitle($serializedProfile['jobTitle']);
        $profile->editTrainingYear($serializedProfile['trainingYear']);
        $profile->editStartOfTraining($serializedProfile['startOfTraining']);
        $profile->editDateOfBirth($serializedProfile['dateOfBirth']);
        $profile->editImage($image, $imageType);

        return $profile;
    }

    /**
     * @param Report $report
     * @return array
     */
    public function serializeReport($report) : array
    {
        return [
            'id' => $report->id(),
            'date' => $report->date(),
            'calendarWeek' => $report->calendarWeek(),
            'calendarYear' => $report->calendarYear(),
            'content' => $report->content(),
            'traineeId' => $report->traineeId(),
            'category' => $report->category(),
            'status' => $report->status()
        ];
    }

    /**
     * @param array $serializedReport
     * @return Report
     */
    public function unserializeReport(array $serializedReport) : Report
    {
        $calendarYear = $serializedReport['calendarYear'];
        $category = $serializedReport['category'];

        if ($calendarYear === null) {
            $calendarYear = explode('.', $serializedReport['date'])[2];
        }

        if ($category === null) {
            $category = Category::COMPANY;
        }

        return new Report(
            new TraineeId($serializedReport['traineeId']),
            $serializedReport['content'],
            $serializedReport['date'],
            $serializedReport['calendarWeek'],
            $calendarYear,
            $serializedReport['id'],
            $category,
            $serializedReport['status']
        );
    }

    /**
     * @param Comment $comment
     * @return array
     */
    public function serializeComment(Comment $comment) : array
    {
        return [
            'id' => $comment->id(),
            'reportId' => $comment->reportId(),
            'userId' => $comment->userId(),
            'date' => $comment->date(),
            'content' => $comment->content(),
            'status' => $comment->status(),
        ];
    }

    /**
     * @param array $serializedComment
     * @return Comment
     */
    public function unserializeComment(array $serializedComment) : Comment
    {
        return new Comment(
            $serializedComment['id'],
            $serializedComment['reportId'],
            $serializedComment['userId'],
            $serializedComment['date'],
            $serializedComment['content'],
            $serializedComment['status']
        );
    }

    /**
     * @param User $user
     * @return string
     */
    public function serializeWebUser(User $user) : string
    {
        return json_encode([
            'role' => $user->roleName(),
            'id' => $user->id(),
            'username' => $user->username(),
            'email' => $user->email()
        ]);
    }

    /**
     * @param Notification $notification
     * @return array
     */
    public function serializeNotification(Notification $notification) : array
    {
        return [
            'id' => $notification->id(),
            'userId' => $notification->userId(),
            'reportId' => $notification->reportId(),
            'title' => $notification->title(),
            'description' => $notification->description(),
            'time' => $notification->time(),
            'status' => $notification->status()
        ];
    }

    /**
     * @param array $serializedNotification
     * @return Notification
     */
    public function unserializeNotification(array $serializedNotification) : Notification
    {
        $notification = new Notification(
            $serializedNotification['title'],
            $serializedNotification['description'],
            $serializedNotification['userId'],
            $serializedNotification['reportId'],
            $serializedNotification['id'],
            $serializedNotification['time']
        );

        if ($serializedNotification['status'] === Notification::STATUS_SEEN) {
            $notification->seen();
        }

        return $notification;
    }
}
