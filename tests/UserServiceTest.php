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
<<<<<<< HEAD
    public function itShouldRegisterUser()
=======
    public function itShouldCreateUser()
>>>>>>> 927c28c72548cb809144f1c04fa1701a1de8993a
    {
        $forename = 'Max';
        $surname = 'Mustermann';
        $email = 'max.mustermann@hotmail.de';
        $role = 'Trainee';
        $password = '123456789';

<<<<<<< HEAD
        $user = $this->userService->registerUser($forename, $surname, $email, $role, $password);
=======
        $user = $this->userService->createUser($forename, $surname, $email, $role, $password);
>>>>>>> 927c28c72548cb809144f1c04fa1701a1de8993a

        $this->assertEquals($forename, $user->forename());
    }
}
