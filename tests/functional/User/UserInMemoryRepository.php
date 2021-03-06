<?php

namespace Jimdo\Reports\functional\User;

use Jimdo\Reports\User\UserRepository;
use Jimdo\Reports\User\User;
use Jimdo\Reports\User\UserId;
use Jimdo\Reports\User\Role;

class UserInMemoryRepository implements UserRepository
{
    const PASSWORD_LENGTH = 7;

    /** @var array */
    public $users = [];

    /** @var bool */
    public $saveMethodCalled = false;

    /**
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return User
     */
    public function createUser(string $username, string $email, Role $role, string $password): User
    {
        if ($this->findUserByEmail($email) !== null) {
            throw new UserRepositoryException("Email already exists!\n");
        }

        if ($this->findUserByUsername($username) !== null) {
            throw new UserRepositoryException("Username already exists!\n");
        }

        if (strlen($password) < self::PASSWORD_LENGTH) {
            throw new UserRepositoryException('Password should have at least ' . self::PASSWORD_LENGTH . ' characters!' . "\n");
        }

        $user = new User($username, $email, $role, $password, new UserId(), true);
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
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername(string $username)
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param string $status
     * @return array
     * @throws UserFileRepositoryException
     */
    public function findUsersByStatus(string $status): array
    {
        $allUsers = $this->findAllUsers();
        $foundUsers = [];

        foreach ($allUsers as $user) {
            if ($user->roleStatus() === $status) {
                $foundUsers[] = $user;
            }
        }
        return $foundUsers;
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

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool
    {
        $username = $this->findUserByUsername($identifier);
        $email = $this->findUserByEmail($identifier);

        if ($username === null && $email === null) {
            return false;
        }
        return true;
    }

    /**
     * @param User $user
     * @throws UserRepositoryException
     */
    public function save(User $user)
    {
        $this->saveMethodCalled = true;
    }
}
