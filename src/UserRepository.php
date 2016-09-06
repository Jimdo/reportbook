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
     * @return User
     */
    public function createUser(string $forename, string $surname, string $email, Role $role, string $password): User;

    /**
     * @param User $deleteUser
     */
    public function deleteUser(User $deleteUser);

    /**
     * @param string $email
     * @return mixed
     */
    public function findUserbyEmail(string $email): User;

    /**
     * @param string $surname
     * @return mixed
     */
    public function findUserbySurname(string $surname): User;

    /**
     * @return array
     */
    public function findAllUsers(): array;
}
