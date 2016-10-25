<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\User\UserId as UserId;
use Jimdo\Reports\Profile\Profile as Profile;
use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\Reportbook\TraineeId as TraineeId;

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
        if ($serializedUser['role']['roleName'] === Role::TRAINEE) {
            $role = new Role(Role::TRAINEE);
        } else {
            $role = new Role(Role::TRAINER);
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
    public function serializeReport(Report $report): array
    {
        return [
            'id' => $report->id(),
            'date' => $report->date(),
            'calendarWeek' => $report->calendarWeek(),
            'content' => $report->content(),
            'traineeId' => $report->traineeId(),
            'status' => $report->status()
        ];
    }

    /**
     * @param array $serializedReport
     * @return Report
     */
    public function unserializeReport(array $serializedReport): Report
    {
        return new Report(
            new TraineeId($serializedReport['traineeId']),
            $serializedReport['content'],
            $serializedReport['date'],
            $serializedReport['calendarWeek'],
            $serializedReport['id'],
            $serializedReport['status']
        );
    }
}
