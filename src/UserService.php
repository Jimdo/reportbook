<?php

namespace Jimdo\Reports;

use Jimdo\Reports\Views\User as ReadOnlyUser;

class UserService
{
    /** @var userRepository */
    private $userRepository;

    /**
     * @param userRepository $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param string $role
     * @param string $password
     */
    public function registerUser(string $forename, string $surname, string $email, string $role, string $password)
    {
        $user = $this->userRepository->createUser($forename, $surname, $email, $role, $password);
        return new ReadOnlyUser($user);
    }
}
