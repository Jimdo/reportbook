<?php

namespace Jimdo\Reports;

use Jimdo\Reports\Role as Role;

interface UserRepository
{
    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return User
     */
    public function createUser(string $forename, string $surname, string $email, Role $role, string $password): User;

    /**
     * @param User $deleteUser
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
}
