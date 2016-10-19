<?php

namespace Jimdo\Reports\User;

use PHPUnit\Framework\TestCase;

use Jimdo\Reports\Views\User as ReadOnlyUser;

class UserServiceTest extends TestCase
{
    /** @var UserService */
    private $userService;

    /** @var UserRepository */
    private $userRepository;

    protected function setUp()
    {
        $this->userRepository = new UserInMemoryRepository();
        $this->userService = new UserService($this->userRepository);
    }

    /**
     * @test
     */
    public function itShouldRegisterTrainee()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee($forename, $surname, 'Hase', $email, $password);

        $this->assertEquals($forename, $user->forename());
    }

    /**
     * @test
     */
    public function itShouldRegisterTrainer()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainer($forename, $surname, 'Hase', $email, $password);

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
        $oldPassword = '123456789';
        $newPassword = '1111111111';

        $user = $this->userService->registerTrainer($forename, $surname, 'Hase', $email, $oldPassword);

        $this->userService->editPassword($user->id(), $oldPassword, $newPassword);

        $user = $this->userService->findUserById($user->id());

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
        $password = '123456789';
        $username = 'jenny';
        $newUsername = 'jennyPenny';

        $user = $this->userService->registerTrainer($forename, $surname, $username, $email, $password);

        $this->userService->editUsername($user->id(), $newUsername);

        $user = $this->userService->findUserById($user->id());

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
        $newEmail = 'jennyPenny@hotmail.de';
        $password = '123456789';
        $username = 'jenny';

        $user = $this->userService->registerTrainer($forename, $surname, $username, $email, $password);

        $this->userService->editEmail($user->id(), $newEmail);

        $user = $this->userService->findUserById($user->id());

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
        $password = '123456789';
        $username = 'jenny';

        $newForename = 'jennypenny';

        $user = $this->userService->registerTrainer($forename, $surname, $username, $email, $password);

        $this->userService->editForename($user->id(), $newForename);

        $user = $this->userService->findUserById($user->id());

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
        $password = '123456789';
        $username = 'jenny';

        $newSurname = 'jennypenny';

        $user = $this->userService->registerTrainer($forename, $surname, $username, $email, $password);

        $this->userService->editSurname($user->id(), $newSurname);

        $user = $this->userService->findUserById($user->id());

        $this->assertEquals($newSurname, $user->surname());
    }

    /**
     * @test
     */
    public function itShouldEditDateOfBirth()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';
        $username = 'jenny';

        $dateOfBirth = '31.10.1995';

        $user = $this->userService->registerTrainer($forename, $surname, $username, $email, $password);

        $this->userService->editDateOfBirth($user->id(), $dateOfBirth);

        $user = $this->userService->findUserById($user->id());

        $this->assertEquals($dateOfBirth, $user->dateOfBirth());
    }

    /**
     * @test
     */
    public function itShouldEditSchool()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';
        $username = 'jenny';

        $school = 'New school';

        $user = $this->userService->registerTrainer($forename, $surname, $username, $email, $password);

        $this->userService->editSchool($user->id(), $school);

        $user = $this->userService->findUserById($user->id());

        $this->assertEquals($school, $user->school());
    }

    /**
     * @test
     */
    public function itShouldEditCompany()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';
        $username = 'jenny';

        $company = 'Jimdo GmbH';

        $user = $this->userService->registerTrainer($forename, $surname, $username, $email, $password);

        $this->userService->editCompany($user->id(), $company);

        $user = $this->userService->findUserById($user->id());

        $this->assertEquals($company, $user->company());
    }

    /**
     * @test
     */
    public function itShouldAuthUser()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $username = 'Hase';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee($forename, $surname, $username, $email, $password);

        $authStatus = $this->userService->authUser($email, $password);
        $this->assertTrue($authStatus);

        $authStatus = $this->userService->authUser($username, $password);
        $this->assertTrue($authStatus);
    }

    /**
     * @test
     */
    public function itShouldHaveRoleName()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee($forename, $surname, 'Hase', $email, $password);

        $this->assertEquals(Role::TRAINEE, $user->roleName());
    }

    /**
     * @test
     */
    public function itShouldApproveRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee($forename, $surname, 'Hase', $email, $password);

        $this->userService->approveRole($user->email());

        $this->assertEquals(Role::STATUS_APPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldDisApproveRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee($forename, $surname, 'Hase', $email, $password);

        $this->userService->disapproveRole($user->email());

        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldSaveStatusAfterApproveOrDisapproveRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $password = '123456789';

        $user = $this->userService->registerTrainee($forename, $surname, 'Hase', $email, $password);

        $this->assertFalse($this->userRepository->saveMethodCalled);
        $this->userService->disapproveRole($user->email());
        $this->assertTrue($this->userRepository->saveMethodCalled);

        $this->userRepository->saveMethodCalled = false;

        $this->assertFalse($this->userRepository->saveMethodCalled);
        $this->userService->approveRole($user->email());
        $this->assertTrue($this->userRepository->saveMethodCalled);
    }

    /**
     * @test
     */
    public function itShouldFindUsersByStatus()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';

        $expectedUser1 = $this->userService->registerTrainee($forename, $surname, 'Hase', $email, '12345678910');
        $users = $this->userService->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(1, $users);

        $expectedUser2 = $this->userService->registerTrainee($forename, $surname, 'Igel', 'maxi.mustermann@hotmail.de', '12345678910');
        $users = $this->userService->findUsersByStatus(Role::STATUS_NOT_APPROVED);
        $this->assertCount(2, $users);

        $expectedUser3 = $this->userService->registerTrainee($forename, $surname, 'Hans', 'peter.mustermann@web.de', '12345678910');
        $this->userService->approveRole($expectedUser3->email());
        $users = $this->userService->findUsersByStatus(Role::STATUS_APPROVED);
        $this->assertCount(1, $users);
    }

    /**
     * @test
     */
    public function itShouldCheckIfUserExistsByEmailOrUsername()
    {
        $username = 'hase2000';
        $mail = 'hase@123.org';

        $this->assertFalse($this->userService->exists($username));
        $this->assertFalse($this->userService->exists($mail));

        $expectedUser1 = $this->userService->registerTrainee('pups', 'hase', $username, $mail, '12345678910');

        $this->assertTrue($this->userService->exists($username));
        $this->assertTrue($this->userService->exists($mail));
    }
}
