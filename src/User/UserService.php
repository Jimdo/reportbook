<?php

namespace Jimdo\Reports\User;

use Jimdo\Reports\Views\User as ReadOnlyUser;
use Jimdo\Reports\User\Role as Role;

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
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    public function registerTrainee(
        string $forename,
        string $surname,
        string $username,
        string $email,
        string $password
    ) {
        $user = $this->registerUser($forename, $surname, $username, $email, new Role(Role::TRAINEE), $password);
        return $user;
    }

    /**
     * @param string $forename
     * @param string $surname
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    public function registerTrainer(
        string $forename,
        string $surname,
        string $username,
        string $email,
        string $password
    ) {
        $user = $this->registerUser($forename, $surname, $username, $email, new Role(Role::TRAINER), $password);
        return $user;
    }

    /**
     * @param string $userId
     * @param string $oldPassword
     * @param string $newPassword
     */
    public function editPassword(string $userId, string $oldPassword, string $newPassword)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editPassword($oldPassword, $newPassword);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $username
     */
    public function editUsername(string $userId, string $username)
    {
        if ($this->exists($username)) {
            throw new ProfileException(
                'The Username already exists!'
            );
        }
        $user = $this->userRepository->findUserById($userId);
        $user->editUsername($username);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $email
     */
    public function editEmail(string $userId, string $email)
    {
        if ($this->exists($email)) {
            throw new ProfileException(
                'The Email already exists!'
            );
        }
        $user = $this->userRepository->findUserById($userId);
        $user->editEmail($email);
        $this->userRepository->save($user);
    }

    /**
     * @param string $userId
     * @param string $forename
     */
    public function editForename(string $userId, string $forename)
    {
        $user = $this->userRepository->findUserById($userId);
        $user->editForename($forename);
        $this->userRepository->save($user);
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authUser(string $identifier, string $password): bool
    {
        $userByMail = $this->userRepository->findUserbyEmail($identifier);
        $userByUsername = $this->userRepository->findUserByUsername($identifier);

        if ($userByMail !== null) {
            if ($userByMail->password() === $password) {
                return true;
            }
        }

        if ($userByUsername !== null) {
            if ($userByUsername->password() === $password) {
                return true;
            }
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
     * @param string $id
     * @return array
     */
    public function findUserById(string $id)
    {
        return $users = $this->userRepository->findUserById($id);
    }

    /**
     * @param string $username
     * @return array
     */
    public function findUserByUsername(string $username)
    {
        return $users = $this->userRepository->findUserByUsername($username);
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

    public function ensureUsersPath()
    {
        $this->userRepository->ensureUsersPath();
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool
    {
        return $this->userRepository->exists($identifier);
    }

    /**
     * @param string $forename
     * @param string $surname
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return ReadOnlyUser
     */
    private function registerUser(
        string $forename,
        string $surname,
        string $username,
        string $email,
        Role $role,
        string $password
    ): ReadOnlyUser {
        $user = $this->userRepository->createUser($forename, $surname, $username, $email, $role, $password);
        return new ReadOnlyUser($user);
    }
}
