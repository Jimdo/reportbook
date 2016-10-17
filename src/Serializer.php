<?php

namespace Jimdo\Reports;

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
            $serializedUser['forename'],
            $serializedUser['surname'],
            $serializedUser['username'],
            $serializedUser['email'],
            $role,
            $serializedUser['password'],
            $serializedUser['id']
        );
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
