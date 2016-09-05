<?php

namespace Jimdo\Reports;

use Jimdo\Reports\Views\User as ReadOnlyUser;
use Jimdo\Reports\Role as Role;

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
     * @param Role $role
     * @param string $password
     */
    public function registerUser(string $forename, string $surname, string $email, Role $role, string $password): ReadOnlyUser
    {
        $user = $this->userRepository->createUser($forename, $surname, $email, $role, $password);
        return new ReadOnlyUser($user);
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authUser(string $email, string $password): bool
    {
        $user = $this->userRepository->findUserbyEmail($email);
        if ($user->password() === $password) {
            return true;
        }
        return false;
    }
}
