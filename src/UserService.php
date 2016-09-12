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
    public function registerTrainee(string $forename, string $surname, string $email, string $password)
    {
        $user = $this->registerUser($forename, $surname, $email, new Role(Role::TRAINEE), $password);
        return $user;
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
     * @param string $status
     * @return array
     */
    public function findUsersByStatus(string $status)
    {
        return $users = $this->userRepository->findUsersByStatus($status);
    }

    /**
     * @param string $status
     * @return array
     */
    public function findUserByEmail(string $email)
    {
        return $users = $this->userRepository->findUserByEmail($email);
    }

    /**
     * @param string $email
     */
    public function approveRole(string $email)
    {
        $user = $this->userRepository->findUserbyEmail($email);
        $user->approve();
        $this->userRepository->save($user);
    }

    /**
     * @param string $email
     */
    public function disapproveRole(string $email)
    {
        $user = $this->userRepository->findUserbyEmail($email);
        $user->disapprove();
        $this->userRepository->save($user);
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
}
