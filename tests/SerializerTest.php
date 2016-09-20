<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;
use Jimdo\Reports\User as User;

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
        $id = uniqid();

        $user = new User($forename, $surname, $username, $email, $role, $password, $id);

        $serialezedUser = $serializer->serializeUser($user);

        $this->assertEquals($user->forename(), $serialezedUser['forename']);
        $this->assertEquals($user->surname(), $serialezedUser['surname']);
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

        $forename = 'Tom';
        $surname = 'Stich';
        $username = 'tomstich';
        $email = 'tom.stich@example.com';
        $role = new Role('TRAINER');
        $password = '1234567';
        $id = uniqid();

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
    }

    /**
     * @test
     */
    public function itShouldSerializeReport()
    {
        $serializer = new Serializer();

        $traineeId = uniqid();
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '35';
        $reportId = uniqid();

        $report = new Report($traineeId, $content, $date, $calendarWeek, $reportId);

        $serializedReport = $serializer->serializeReport($report);

        $this->assertEquals($traineeId, $serializedReport['traineeId']);
        $this->assertEquals($content, $serializedReport['content']);
        $this->assertEquals($date, $serializedReport['date']);
        $this->assertEquals($calendarWeek, $serializedReport['calendarWeek']);
        $this->assertEquals($reportId, $serializedReport['id']);
    }

    /**
     * @test
     */
    public function itShouldUnserializeReport()
    {
        $serializer = new Serializer();

        $traineeId = uniqid();
        $content = 'some content';
        $date = '10.10.10';
        $calendarWeek = '35';
        $reportId = uniqid();

        $report = new Report($traineeId, $content, $date, $calendarWeek, $reportId);

        $serializedReport = $serializer->serializeReport($report);

        $unserializedReport = $serializer->unserializeReport($serializedReport);

        $this->assertEquals($traineeId, $unserializedReport->traineeId());
        $this->assertEquals($content, $unserializedReport->content());
        $this->assertEquals($date, $unserializedReport->date());
        $this->assertEquals($calendarWeek, $unserializedReport->calendarWeek());
        $this->assertEquals($reportId, $unserializedReport->id());
    }
}
