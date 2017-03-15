<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\Reportbook\Comment as Comment;
use Jimdo\Reports\User\UserId as UserId;
use Jimdo\Reports\Profile\Profile as Profile;
use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\Reportbook\TraineeId as TraineeId;
use Jimdo\Reports\Reportbook\Category as Category;

class Serializer
{
    /**
     * @param User $user
     * @return array
     */
    public function serializeUser(User $user): array
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
    public function unserializeUser(array $serializedUser): User
    {
        switch (strtoupper($serializedUser['role']['roleName'])) {
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

        if ($serializedUser['role']['roleStatus'] === Role::STATUS_APPROVED) {
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
    public function serializeProfile(Profile $profile): array
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
    public function unserializeProfile(array $serializedProfile): Profile
    {
        $profile = new Profile(
            $serializedProfile['userId'],
            $serializedProfile['forename'],
            $serializedProfile['surname']
        );

        $profile->editDateOfBirth($serializedProfile['dateOfBirth']);
        $profile->editCompany($serializedProfile['company']);
        $profile->editJobTitle($serializedProfile['jobTitle']);
        $profile->editSchool($serializedProfile['school']);
        $profile->editGrade($serializedProfile['grade']);
        $profile->editTrainingYear($serializedProfile['trainingYear']);
        $profile->editStartOfTraining($serializedProfile['startOfTraining']);
        $profile->editImage($serializedProfile['image']['base64'], $serializedProfile['image']['type']);

        return $profile;
    }

    /**
     * @param Report $report
     * @return array
     */
    public function serializeReport($report): array
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
    public function unserializeReport(array $serializedReport): Report
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
    public function serializeComment(Comment $comment): array
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
    public function unserializeComment(array $serializedComment): Comment
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
}
