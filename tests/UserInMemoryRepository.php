<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User as User;
use Jimdo\Reports\Role as Role;

class UserInMemoryRepository implements UserRepository
{
    const PASSWORD_LENGTH = 7;

    /** @var array */
    public $users = [];

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return User
     */
    public function createUser(string $forename, string $surname, string $email, Role $role, string $password): User
    {
        if ($this->findUserByEmail($email) !== null) {
            throw new UserRepositoryException("Email already exists!\n");
        }

        if (strlen($password) < self::PASSWORD_LENGTH) {
            throw new UserRepositoryException('Password should have at least ' . self::PASSWORD_LENGTH . ' characters!' . "\n");
        }

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
     * @return User|null
     */
    public function findUserByEmail(string $email)
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
     * @return User|null
     */
    public function findUserById(string $id)
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
     * @return User|null
     */
    public function findUserBySurname(string $surname)
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
