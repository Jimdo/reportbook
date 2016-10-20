<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\User\UserId as UserId;
use Jimdo\Reports\Reportbook\TraineeId as TraineeId;
use Jimdo\Reports\Reportbook\Report as Report;

class SerializerTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldSerializeUser()
    {
        $serializer = new Serializer();

        $forename = 'Tom';
        $surname = 'Stich';
        $username = 'tomstich';
        $email = 'tom.stich@example.com';
        $role = new Role('TRAINER');
        $password = '1234567';
        $id = new UserId();
        $dateOfBirth = '10.10.10';
        $company = 'Jimdo';
        $grade = '1';
        $school = 'bla';
        $trainingYear = '2';
        $jobTitle = 'programmer';
        $startOfTraining = '20.20.20';

        $user = new User($forename, $surname, $username, $email, $role, $password, $id);

        $user->editDateOfBirth($dateOfBirth);
        $user->editCompany($company);
        $user->editGrade($grade);
        $user->editSchool($school);
        $user->editTrainingYear($trainingYear);
        $user->editJobTitle($jobTitle);
        $user->editJobTitle($startOfTraining);

        $serialezedUser = $serializer->serializeUser($user);

        $this->assertEquals($user->forename(), $serialezedUser['forename']);
        $this->assertEquals($user->surname(), $serialezedUser['surname']);
        $this->assertEquals($user->username(), $serialezedUser['username']);
        $this->assertEquals($user->email(), $serialezedUser['email']);
        $this->assertEquals($user->roleName(), $serialezedUser['role']['roleName']);
        $this->assertEquals($user->roleStatus(), $serialezedUser['role']['roleStatus']);
        $this->assertEquals($user->password(), $serialezedUser['password']);
        $this->assertEquals($user->id(), $serialezedUser['id']);
        $this->assertEquals($user->dateOfBirth(), $serialezedUser['dateOfBirth']);
        $this->assertEquals($user->company(), $serialezedUser['company']);
        $this->assertEquals($user->school(), $serialezedUser['school']);
        $this->assertEquals($user->grade(), $serialezedUser['grade']);
        $this->assertEquals($user->trainingYear(), $serialezedUser['trainingYear']);
        $this->assertEquals($user->jobTitle(), $serialezedUser['jobTitle']);
        $this->assertEquals($user->startOfTraining(), $serialezedUser['startOfTraining']);
    }

    /**
     * @test
     */
    public function itShouldUnserializeUser()
    {
        $serializer = new Serializer();

        $forename = 'Tom';
        $surname = 'Stich';
        $username = 'tomstich';
        $email = 'tom.stich@example.com';
        $role = new Role('TRAINER');
        $password = '1234567';
        $id = new UserId();
        $dateOfBirth = '10.10.10';
        $company = 'Jimdo';
        $grade = '1';
        $school = 'bla';
        $trainingYear = '2';
        $jobTitle = 'programmer';
        $startOfTraining = '20.20.20';

        $user = new User($forename, $surname, $username, $email, $role, $password, $id);

        $serializedUser = $serializer->serializeUser($user);

        $unserializedUser = $serializer->unserializeUser($serializedUser);

        $this->assertEquals($user->forename(), $unserializedUser->forename());
        $this->assertEquals($user->surname(), $unserializedUser->surname());
        $this->assertEquals($user->username(), $unserializedUser->username());
        $this->assertEquals($user->email(), $unserializedUser->email());
        $this->assertEquals($user->roleName(), $unserializedUser->roleName());
        $this->assertEquals($user->roleStatus(), $unserializedUser->roleStatus());
        $this->assertEquals($user->password(), $unserializedUser->password());
        $this->assertEquals($user->id(), $unserializedUser->id());
        $this->assertEquals($user->dateOfBirth(), $unserializedUser->dateOfBirth());
        $this->assertEquals($user->company(), $unserializedUser->company());
        $this->assertEquals($user->school(), $unserializedUser->school());
        $this->assertEquals($user->grade(), $unserializedUser->grade());
        $this->assertEquals($user->trainingYear(), $unserializedUser->trainingYear());
        $this->assertEquals($user->jobTitle(), $unserializedUser->jobTitle());
        $this->assertEquals($user->startOfTraining(), $unserializedUser->startOfTraining());
    }

    /**
     * @test
     */
    public function itShouldSerializeReport()
    {
        $serializer = new Serializer();

        $traineeId = new TraineeId();
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '35';
        $reportId = uniqid();

        $report = new Report($traineeId, $content, $date, $calendarWeek, $reportId);

        $serializedReport = $serializer->serializeReport($report);

        $this->assertEquals($traineeId->id(), $serializedReport['traineeId']);
        $this->assertEquals($content, $serializedReport['content']);
        $this->assertEquals($date, $serializedReport['date']);
        $this->assertEquals($calendarWeek, $serializedReport['calendarWeek']);
        $this->assertEquals($reportId, $serializedReport['id']);
        $this->assertEquals($report->status(), $serializedReport['status']);
    }

    /**
     * @test
     */
    public function itShouldUnserializeReport()
    {
        $serializer = new Serializer();

        $traineeId = new TraineeId();
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '35';
        $reportId = uniqid();

        $report = new Report($traineeId, $content, $date, $calendarWeek, $reportId);

        $serializedReport = $serializer->serializeReport($report);

        $unserializedReport = $serializer->unserializeReport($serializedReport);

        $this->assertEquals($traineeId->id(), $unserializedReport->traineeId());
        $this->assertEquals($content, $unserializedReport->content());
        $this->assertEquals($date, $unserializedReport->date());
        $this->assertEquals($calendarWeek, $unserializedReport->calendarWeek());
        $this->assertEquals($reportId, $unserializedReport->id());
        $this->assertEquals($report->status(), $unserializedReport->status());
    }
}
