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
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    private function registerUser(string $forename, string $surname, string $email, Role $role, string $password): ReadOnlyUser
    {
        $user = $this->userRepository->createUser($forename, $surname, $email, $role, $password);
        return new ReadOnlyUser($user);
    }

    public function registerTrainee(string $forename, string $surname, string $email, string $password)
    {
        $user = $this->registerUser($forename, $surname, $email, new Role(Role::TRAINEE), $password);
        return $user;
    }

    public function registerTrainer(string $forename, string $surname, string $email, string $password)
    {
        $user = $this->registerUser($forename, $surname, $email, new Role(Role::TRAINER), $password);
        return $user;
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

    /**
     * @param string $email
     */
    public function approveRole(string $email)
    {
        $user = $this->userRepository->findUserbyEmail($email);
        $user->approve();
    }

    /**
     * @param string $email
     */
    public function disapproveRole(string $email)
    {
        $user = $this->userRepository->findUserbyEmail($email);
        $user->disapprove();
    }
}
