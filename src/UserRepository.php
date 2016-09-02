<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User as User;

class UserRepository
{
    /** @var array */
    public $users = [];

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param string $role
     * @return User
     */
    public function createUser(string $forename, string $surname, string $email, string $role)
    {
        $user = new User($forename, $surname, $email, $role);
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
    public function findUserbyEmail(string $email)
    {
        foreach ($this->users as $user) {
            if ($user->email() === $email) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param string $surname
     * @return mixed
     */
    public function findUserbySurname(string $surname)
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
    public function findAllUsers()
    {
        return $this->users;
    }
}
