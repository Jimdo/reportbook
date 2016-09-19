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

        $this->assertEquals($forename, $serialezedUser['forename']);
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

        $this->assertEquals($forename, $unserializedUser->forename());
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

        $this->assertEquals($content, $serializedReport['content']);
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

        $this->assertEquals($content, $unserializedReport->content());
    }
}
