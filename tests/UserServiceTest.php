<?php

namespace Jimdo\Reports;

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
        $this->userRepository = new UserRepository();
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
        $role = new Role('trainee');
        $password = '123456789';

        $user = $this->userService->registerUser($forename, $surname, $email, $role, $password);

        $this->assertEquals($forename, $user->forename());
    }

    /**
     * @test
     */
    public function itShouldAuthUser()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '123456789';

        $user = $this->userService->registerUser($forename, $surname, $email, $role, $password);
        $authStatus = $this->userService->authUser($email, $password);

        $this->assertTrue($authStatus);
    }

    /**
     * @test
     */
    public function itShouldHaveRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '123456789';

        $user = $this->userService->registerUser($forename, $surname, $email, $role, $password);

        $this->assertEquals($role, $user->role());
    }

    /**
     * @test
     */
    public function itShouldApproveRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '123456789';

        $user = $this->userService->registerUser($forename, $surname, $email, $role, $password);

        $this->userService->approveRole($user->email());

        $this->assertEquals(Role::STATUS_APPROVED, $user->role()->status());
    }

    /**
     * @test
     */
    public function itShouldDisApproveRole()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = new Role('trainee');
        $password = '123456789';

        $user = $this->userService->registerUser($forename, $surname, $email, $role, $password);

        $this->userService->disapproveRole($user->email());

        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->role()->status());
    }
}
