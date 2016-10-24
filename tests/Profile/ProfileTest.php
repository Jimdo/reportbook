<?php

namespace Jimdo\Reports\Profile;

use Jimdo\Reports\User\User as User;
use Jimdo\Reports\User\Role as Role;
use Jimdo\Reports\User\UserId as UserId;
use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveProfileData()
    {
        $forename = 'hauke';
        $surname = 'mauke';

        $user = new User($forename, $surname, 'maxmusti', 'maxmusti@hotmail.de', new Role('TRAINER'), 'geheim123', new UserId());

        $userId = $user->id();


        $profile = new Profile($user->id(), $forename, $surname);

        $this->assertEquals($user->id(), $profile->userId());
        $this->assertEquals($forename, $profile->forename());
        $this->assertEquals($surname, $profile->surname());
        $this->assertEquals('', $profile->school());
        $this->assertEquals('', $profile->dateOfBirth());
        $this->assertEquals('', $profile->grade());
        $this->assertEquals('', $profile->jobTitle());
        $this->assertEquals('', $profile->trainingYear());
        $this->assertEquals('', $profile->company());
        $this->assertEquals('', $profile->startOfTraining());
        $this->assertEquals('', $profile->image());
    }
}
