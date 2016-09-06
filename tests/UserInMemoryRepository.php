<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User as User;
use Jimdo\Reports\Role as Role;

class UserInMemoryRepository implements UserRepository
{
    /** @var array */
    public $users = [];

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param Role $role
     * @param string $password
     * @return User
     */
    public function createUser(string $forename, string $surname, string $email, Role $role, string $password): User
    {
        $user = new User($forename, $surname, $email, $role, $password);
        $this->users[] = $user;
        return $user;
    }

    /**
     * @param User $deleteUser
     */
    public function deleteUser(User $deleteUser)
    {
        foreach ($this->users as $key => $user) {
            if ($user === $deleteUser) {
                unset($this->users[$key]);
            }
        }
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function findUserByEmail(string $email): User
    {
        foreach ($this->users as $user) {
            if ($user->email() === $email) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function findUserById(string $id): User
    {
        foreach ($this->users as $user) {
            if ($user->id() === $id) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param string $surname
     * @return mixed
     */
    public function findUserBySurname(string $surname): User
    {
        foreach ($this->users as $user) {
            if ($user->surname() === $surname) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function findAllUsers(): array
    {
        return $this->users;
    }
}
