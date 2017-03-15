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
        $userId = uniqid();
        $forename = 'hauke';
        $surname = 'mauke';
        $userId = uniqid();


        $profile = new Profile($userId, $forename, $surname);

        $this->assertEquals($userId, $profile->userId());
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

    /**
    * @test
    */
    public function itShouldEditForename()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newForename = 'Jenny';

        $profile->editForename($newForename);

        $this->assertEquals($newForename, $profile->forename());
    }

    /**
    * @test
    */
    public function itShouldEditSurname()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newSurname = 'Penny';

        $profile->editSurname($newSurname);

        $this->assertEquals($newSurname, $profile->surname());
    }

    /**
    * @test
    */
    public function itShouldEditSchool()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newSchool = 'Penny in Hamburg';

        $profile->editSchool($newSchool);

        $this->assertEquals($newSchool, $profile->school());
    }

    /**
    * @test
    */
    public function itShouldEditJobTitle()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newJobTitle = 'Fachinformatiker Anwendungsentwicklung';

        $profile->editJobTitle($newJobTitle);

        $this->assertEquals($newJobTitle, $profile->jobTitle());
    }

    /**
    * @test
    */
    public function itShouldEditStartOfTraining()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newStartOfTraining = '12.12.12';

        $profile->editStartOfTraining($newStartOfTraining);

        $this->assertEquals($newStartOfTraining, $profile->startOfTraining());
    }

    /**
    * @test
    */
    public function itShouldEditTrainingYear()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newTrainingYear = 1;

        $profile->editTrainingYear($newTrainingYear);

        $this->assertEquals($newTrainingYear, $profile->trainingYear());
    }

    /**
    * @test
    */
    public function itShouldEditCompany()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newCompany = 'Jimdo GmbH';

        $profile->editCompany($newCompany);

        $this->assertEquals($newCompany, $profile->company());
    }

    /**
    * @test
    */
    public function itShouldEditImage()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newImage = '321321321321321321321231312';

        $profile->editImage($newImage, 'png');

        $this->assertEquals($newImage, $profile->image());
    }

    /**
    * @test
    */
    public function itShouldEditDateOfBirth()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'SecurePassword123';
        $username = 'jenny';

        $user = new User($username, $email, $role, $password, new UserId());

        $profile = new Profile($user->id(), $forename, $surname);

        $newDateOfBirth = '31.10.1995';

        $profile->editDateOfBirth($newDateOfBirth);

        $this->assertEquals($newDateOfBirth, $profile->dateOfBirth());
    }
}
