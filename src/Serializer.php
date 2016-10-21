<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\User\UserId as UserId;
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
            'forename' => $user->forename(),
            'surname' => $user->surname(),
            'username' => $user->username(),
            'email' => $user->email(),
            'role' => [
                'roleName' => $user->roleName(),
                'roleStatus' => $user->roleStatus()
                ],
            'password' => $user->password(),
            'dateOfBirth' => $user->dateOfBirth(),
            'school' => $user->school(),
            'grade' => $user->grade(),
            'trainingYear' => $user->trainingYear(),
            'company' => $user->company(),
            'jobTitle' => $user->jobTitle(),
            'startOfTraining' => $user->startOfTraining(),
            'image' => $user->image(),
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

        $user = new User(
            $serializedUser['forename'],
            $serializedUser['surname'],
            $serializedUser['username'],
            $serializedUser['email'],
            $role,
            $serializedUser['password'],
            new UserId($serializedUser['id'])
        );

        $user->editDateOfBirth($serializedUser['dateOfBirth']);
        $user->editSchool($serializedUser['school']);
        $user->editGrade($serializedUser['grade']);
        $user->editTrainingYear($serializedUser['trainingYear']);
        $user->editCompany($serializedUser['company']);
        $user->editJobTitle($serializedUser['jobTitle']);
        $user->editStartOfTraining($serializedUser['startOfTraining']);
        $user->editImage($serializedUser['image']);

        return $user;
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
