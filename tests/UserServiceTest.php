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

        $user = $this->userService->registerTrainee($forename, $surname, $email, $password);

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

        $user = $this->userService->registerTrainer($forename, $surname, $email, $password);

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
        $password = '123456789';

        $user = $this->userService->registerTrainee($forename, $surname, $email, $password);
        $authStatus = $this->userService->authUser($email, $password);

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

        $user = $this->userService->registerTrainee($forename, $surname, $email, $password);

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

        $user = $this->userService->registerTrainee($forename, $surname, $email, $password);

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

        $user = $this->userService->registerTrainee($forename, $surname, $email, $password);

        $this->userService->disapproveRole($user->email());

        $this->assertEquals(Role::STATUS_DISAPPROVED, $user->roleStatus());
    }

    /**
     * @test
     */
    public function itShouldFindUsersByStatus()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';

        $expectedUser1 = $this->userService->registerTrainee($forename, $surname, $email, '12345678910');
        $users = $this->userService->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(1, $users);

        $expectedUser2 = $this->userService->registerTrainee($forename, $surname, 'maxi.mustermann@hotmail.de', '12345678910');
        $users = $this->userService->findUsersByStatus(Role::STATUS_NOT_APPROVED);

        $this->assertCount(2, $users);
    }
}
