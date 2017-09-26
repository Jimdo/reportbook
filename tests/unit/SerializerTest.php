<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\Reportbook\Comment as Comment;
use Jimdo\Reports\User\UserId as UserId;
use Jimdo\Reports\Profile\Profile as Profile;
use Jimdo\Reports\Reportbook\TraineeId as TraineeId;
use Jimdo\Reports\Reportbook\Report as Report;
use Jimdo\Reports\Reportbook\Category as Category;
use Jimdo\Reports\Notification\BrowserNotification as Notification;

class SerializerTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldSerializeUser()
    {
        $serializer = new Serializer();

        $username = 'tomstich';
        $email = 'tom.stich@example.com';
        $role = new Role('TRAINER');
        $password = 'SecurePassword123';
        $id = new UserId();

        $user = new User($username, $email, $role, $password, $id, false);

        $serialezedUser = $serializer->serializeUser($user);

        $this->assertEquals($user->username(), $serialezedUser['username']);
        $this->assertEquals($user->email(), $serialezedUser['email']);
        $this->assertEquals($user->roleName(), $serialezedUser['role']['roleName']);
        $this->assertEquals($user->roleStatus(), $serialezedUser['role']['roleStatus']);
        $this->assertEquals($user->password(), $serialezedUser['password']);
        $this->assertEquals($user->id(), $serialezedUser['id']);
    }

    /**
     * @test
     */
    public function itShouldUnserializeUser()
    {
        $serializer = new Serializer();

        $username = 'tomstich';
        $email = 'tom.stich@example.com';
        $role = new Role('TRAINER');
        $password = 'SecurePassword123';
        $id = new UserId();

        $user = new User($username, $email, $role, $password, $id, false);

        $serializedUser = $serializer->serializeUser($user);

        $unserializedUser = $serializer->unserializeUser($serializedUser);

        $this->assertEquals($user->username(), $unserializedUser->username());
        $this->assertEquals($user->email(), $unserializedUser->email());
        $this->assertEquals($user->roleName(), $unserializedUser->roleName());
        $this->assertEquals($user->roleStatus(), $unserializedUser->roleStatus());
        $this->assertEquals($user->password(), $unserializedUser->password());
        $this->assertEquals($user->id(), $unserializedUser->id());
    }

    /**
     * @test
     */
    public function itShouldSerializeProfile()
    {
        $serializer = new Serializer();

        $forename = 'Tom';
        $surname = 'Stich';
        $id = uniqId();
        $dateOfBirth = '10.10.10';
        $company = 'Jimdo';
        $grade = '1';
        $school = 'bla';
        $trainingYear = '2';
        $jobTitle = 'programmer';
        $startOfTraining = '20.20.20';

        $profile = new Profile($id, $forename, $surname);

        $profile->editDateOfBirth($dateOfBirth);
        $profile->editCompany($company);
        $profile->editGrade($grade);
        $profile->editSchool($school);
        $profile->editTrainingYear($trainingYear);
        $profile->editJobTitle($jobTitle);
        $profile->editStartOfTraining($startOfTraining);

        $serializedProfile = $serializer->serializeProfile($profile);

        $this->assertEquals($profile->forename(), $serializedProfile['forename']);
        $this->assertEquals($profile->surname(), $serializedProfile['surname']);
        $this->assertEquals($profile->userId(), $serializedProfile['userId']);
        $this->assertEquals($profile->dateOfBirth(), $serializedProfile['dateOfBirth']);
        $this->assertEquals($profile->company(), $serializedProfile['company']);
        $this->assertEquals($profile->school(), $serializedProfile['school']);
        $this->assertEquals($profile->grade(), $serializedProfile['grade']);
        $this->assertEquals($profile->trainingYear(), $serializedProfile['trainingYear']);
        $this->assertEquals($profile->jobTitle(), $serializedProfile['jobTitle']);
        $this->assertEquals($profile->startOfTraining(), $serializedProfile['startOfTraining']);
    }

    /**
     * @test
     */
    public function itShouldUnserializeProfile()
    {
        $serializer = new Serializer();

        $forename = 'Tom';
        $surname = 'Stich';
        $id = uniqId();

        $profile = new Profile($id, $forename, $surname);

        $serializedProfile = $serializer->serializeProfile($profile);

        $unserializedProfile = $serializer->unserializeProfile($serializedProfile);

        $this->assertEquals($profile->forename(), $unserializedProfile->forename());
        $this->assertEquals($profile->surname(), $unserializedProfile->surname());
        $this->assertEquals($profile->userId(), $unserializedProfile->userId());
        $this->assertEquals($profile->dateOfBirth(), $unserializedProfile->dateOfBirth());
        $this->assertEquals($profile->company(), $unserializedProfile->company());
        $this->assertEquals($profile->school(), $unserializedProfile->school());
        $this->assertEquals($profile->grade(), $unserializedProfile->grade());
        $this->assertEquals($profile->trainingYear(), $unserializedProfile->trainingYear());
        $this->assertEquals($profile->jobTitle(), $unserializedProfile->jobTitle());
        $this->assertEquals($profile->startOfTraining(), $unserializedProfile->startOfTraining());
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
        $calendarYear = '2016';
        $reportId = uniqid();
        $category = Category::SCHOOL;

        $report = new Report($traineeId, $content, $date, $calendarWeek, $calendarYear, $reportId, $category);

        $serializedReport = $serializer->serializeReport($report);

        $this->assertEquals($traineeId->id(), $serializedReport['traineeId']);
        $this->assertEquals($content, $serializedReport['content']);
        $this->assertEquals($date, $serializedReport['date']);
        $this->assertEquals($calendarWeek, $serializedReport['calendarWeek']);
        $this->assertEquals($reportId, $serializedReport['id']);
        $this->assertEquals($report->status(), $serializedReport['status']);
        $this->assertEquals($report->category(), $serializedReport['category']);
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
        $calendarYear = '2016';
        $reportId = uniqid();
        $category = Category::SCHOOL;

        $report = new Report($traineeId, $content, $date, $calendarWeek, $calendarYear, $reportId, $category);

        $serializedReport = $serializer->serializeReport($report);

        $unserializedReport = $serializer->unserializeReport($serializedReport);

        $this->assertEquals($traineeId->id(), $unserializedReport->traineeId());
        $this->assertEquals($content, $unserializedReport->content());
        $this->assertEquals($date, $unserializedReport->date());
        $this->assertEquals($calendarWeek, $unserializedReport->calendarWeek());
        $this->assertEquals($reportId, $unserializedReport->id());
        $this->assertEquals($report->status(), $unserializedReport->status());
        $this->assertEquals($report->category(), $unserializedReport->category());
    }

    /**
     * @test
     */
    public function itShouldSerializeComment()
    {
        $serializer = new Serializer();

        $id = uniqid();
        $reportId = uniqid();
        $userId = uniqid();
        $date = '10.10.10';
        $content = 'some content';

        $comment = new Comment($id, $reportId, $userId, $date, $content);

        $serializedComment = $serializer->serializeComment($comment);

        $this->assertEquals($id, $serializedComment['id']);
        $this->assertEquals($reportId, $serializedComment['reportId']);
        $this->assertEquals($userId, $serializedComment['userId']);
        $this->assertEquals($date, $serializedComment['date']);
        $this->assertEquals($content, $serializedComment['content']);
        $this->assertEquals($comment->status(), $serializedComment['status']);
    }

    /**
     * @test
     */
    public function itShouldUnserializeComment()
    {
        $serializer = new Serializer();

        $id = uniqid();
        $reportId = uniqid();
        $userId = uniqid();
        $date = '10.10.10';
        $content = 'some content';

        $comment = new Comment($id, $reportId, $userId, $date, $content);

        $serializedComment = $serializer->serializeComment($comment);

        $unserializedComment = $serializer->unserializeComment($serializedComment);

        $this->assertEquals($id, $unserializedComment->id());
        $this->assertEquals($reportId, $unserializedComment->reportId());
        $this->assertEquals($userId, $unserializedComment->userId());
        $this->assertEquals($date, $unserializedComment->date());
        $this->assertEquals($content, $unserializedComment->content());
        $this->assertEquals($comment->status(), $unserializedComment->status());
    }

    /**
     * @test
     */
    public function itShouldSerializeNotification()
    {
        $serializer = new Serializer();

        $title = 'title';
        $description = 'description';
        $userId =  uniqid();
        $reportId =  uniqid();

        $notification = new Notification($title, $description, $userId, $reportId);

        $serializedNotification = $serializer->serializeNotification($notification);

        $this->assertEquals($title, $serializedNotification['title']);
        $this->assertEquals($description, $serializedNotification['description']);
        $this->assertEquals($userId, $serializedNotification['userId']);
        $this->assertEquals($reportId, $serializedNotification['reportId']);
        $this->assertEquals($notification->time(), $serializedNotification['time']);
        $this->assertEquals($notification->id(), $serializedNotification['id']);
        $this->assertEquals($notification->status(), $serializedNotification['status']);
    }

    /**
     * @test
     */
    public function itShouldUnserializeNotification()
    {
        $serializer = new Serializer();

        $title = 'title';
        $description = 'description';
        $userId =  uniqid();
        $reportId =  uniqid();

        $notification = new Notification($title, $description, $userId, $reportId);

        $serializedNotification = $serializer->serializeNotification($notification);

        $unserializedNotification = $serializer->unserializeNotification($serializedNotification);

        $this->assertEquals($title, $unserializedNotification->title());
        $this->assertEquals($description, $unserializedNotification->description());
        $this->assertEquals($userId, $unserializedNotification->userId());
        $this->assertEquals($reportId, $unserializedNotification->reportId());
        $this->assertEquals($notification->time(), $unserializedNotification->time());
        $this->assertEquals($notification->id(), $unserializedNotification->id());
        $this->assertEquals($notification->status(), $unserializedNotification->status());
    }
}
