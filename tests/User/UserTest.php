<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
    * @test
    */
    public function itShouldHaveUserConstruct()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = 'strongpassword';
        $user = new User($forename, $surname, 'Hase', $email, $role, $password, new UserId());

        $this->assertEquals($forename, $user->forename());
        $this->assertEquals($surname, $user->surname());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($password, $user->password());
        $this->assertInternalType('string', $user->id());
        $this->assertEquals('', $user->dateOfBirth());
        $this->assertEquals('', $user->school());
        $this->assertEquals('', $user->grade());
        $this->assertEquals('', $user->jobTitle());
        $this->assertEquals('', $user->trainingYear());
        $this->assertEquals('', $user->company());
        $this->assertEquals('', $user->startOfTraining());
    }

    /**
    * @test
    */
    public function itShouldEditUser()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $user = new User($forename, $surname, 'Hase', $email, $role, '12345678910', new UserId());

        $this->assertEquals($forename, $user->forename());

        $forename = 'Peter';

        $user->edit($forename, $surname, 'Hase', $email, '12345678910');

        $this->assertEquals($forename, $user->forename());
    }

    /**
    * @test
    */
    public function itShouldEditPassword()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $oldPassword = '1111111';
        $user = new User($forename, $surname, 'Hase', $email, $role, $oldPassword, new UserId());

        $newPassword = 'peterlustig';

        $user->editPassword($oldPassword, $newPassword);

        $this->assertEquals($newPassword, $user->password());
    }

    /**
    * @test
    */
    public function itShouldEditUsername()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newUsername = 'jennypenny';

        $user->editUsername($newUsername);

        $this->assertEquals($newUsername, $user->username());
    }

    /**
    * @test
    */
    public function itShouldEditEmail()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newEmail = 'jenny@hotmail.de';

        $user->editEmail($newEmail);

        $this->assertEquals($newEmail, $user->email());
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
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newForename = 'Jenny';

        $user->editForename($newForename);

        $this->assertEquals($newForename, $user->forename());
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
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newSurname = 'Penny';

        $user->editSurname($newSurname);

        $this->assertEquals($newSurname, $user->surname());
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
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newSchool = 'Penny in Hamburg';

        $user->editSchool($newSchool);

        $this->assertEquals($newSchool, $user->school());
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
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newJobTitle = 'Fachinformatiker Anwendungsentwicklung';

        $user->editJobTitle($newJobTitle);

        $this->assertEquals($newJobTitle, $user->jobTitle());
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
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newStartOfTraining = '12.12.12';

        $user->editStartOfTraining($newStartOfTraining);

        $this->assertEquals($newStartOfTraining, $user->startOfTraining());
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
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newTrainingYear = 1;

        $user->editTrainingYear($newTrainingYear);

        $this->assertEquals($newTrainingYear, $user->trainingYear());
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
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newCompany = 'Jimdo GmbH';

        $user->editCompany($newCompany);

        $this->assertEquals($newCompany, $user->company());
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
        $password = '1111111';
        $username = 'jenny';
        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());

        $newDateOfBirth = '31.10.1995';

        $user->editDateOfBirth($newDateOfBirth);

        $this->assertEquals($newDateOfBirth, $user->dateOfBirth());
    }

    /**
    * @test
    */
    public function itShouldHaveRoleConstruct()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $roleName = 'trainee';
        $user = new User($forename, $surname, 'Hase', $email, new Role($roleName), '12345678910', new UserId());

        $this->assertEquals($roleName, $user->roleName());

        $this->assertEquals(Role::STATUS_NOT_APPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldChangeStatusOfRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $user = new User($forename, $surname, 'Hase', $email, $role, '12345678910', new UserId());

        $user->approve();
        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());

        $user->disapprove();
        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }
}
