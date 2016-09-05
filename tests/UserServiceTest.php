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
    public function itShouldRegisterUser()
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = 'Trainee';
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
        $role = 'Trainee';
        $password = '123456789';

        $user = $this->userService->registerUser($forename, $surname, $email, $role, $password);
        $authStatus = $this->userService->authUser($email, $password);

        $this->assertTrue($authStatus);
    }
}
