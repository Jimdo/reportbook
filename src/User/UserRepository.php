<?php

namespace Jimdo\Reports\User;

use Jimdo\Reports\User\Role as Role;

interface UserRepository
{
    /**
     * @param string $forename
     * @param string $surname
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return User
     */
    public function createUser(
        string $forename,
        string $surname,
        string $username,
        string $email,
        Role $role,
        string $password
    ): User;

    /**
     * @param User $user
     * @throws UserRepositoryException
     */
    public function save(User $user);

    /**
     * @param User $deleteUser
     * @throws UserRepositoryException
     */
    public function deleteUser(User $deleteUser);

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email);

    /**
     * @param string $surname
     * @return User|null
     */
    public function findUserBySurname(string $surname);

    /**
     * @return array
     */
    public function findAllUsers(): array;

    /**
     * @param string $id
     * @return User|null
     */
    public function findUserById(string $id);

    /**
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername(string $username);

    /**
     * @param string $status
     * @return array
     * @throws UserFileRepositoryException
     */
    public function findUsersByStatus(string $status): array;

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool;
}
