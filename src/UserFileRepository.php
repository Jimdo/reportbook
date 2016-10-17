<?php

namespace Jimdo\Reports;

class UserFileRepository implements UserRepository
{


    /** @var string */
    private $usersPath;

    /**
     * @param string $usersPath
     */
    public function __construct(string $usersPath)
    {
         $this->usersPath = $usersPath;
    }

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
    ): User {
        if ($this->findUserByEmail($email) !== null) {
            throw new UserRepositoryException("Email already exists!\n");
        }

        $user = new User($forename, $surname, $username, $email, $role, $password, new UserId());
        $this->ensureUsersPath();
        $this->save($user);

        return $user;
    }

    /**
     * @param User $user
     * @throws UserRepositoryException
     */
    public function save(User $user)
    {
        $this->ensureUsersPath();
        $filename = $this->filename($user);
        if (file_put_contents($filename, serialize($user)) === false) {
            throw new UserRepositoryException("Could not write to $filename!");
        }
    }

    /**
     * @param User $deleteUser
     * @throws UserRepositoryException
     */
    public function deleteUser(User $deleteUser)
    {
        $filename = $this->filename($deleteUser);
        if (!unlink($filename)) {
            throw new UserRepositoryException("Could not delete $filename!");
        }
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email)
    {
        $allUsers = $this->findAllUsers();

        foreach ($allUsers as $user) {
            if ($user->email() === $email) {
                return $user;
            }
        }
    }

    /**
     * @param string $surname
     * @return User|null
     */
    public function findUserBySurname(string $surname)
    {
        $allUsers = $this->findAllUsers();

        foreach ($allUsers as $user) {
            if ($user->surname() === $surname) {
                return $user;
            }
        }
    }

    /**
     * @return array
     */
    public function findAllUsers(): array
    {
        $foundUsers = [];
        $this->ensureUsersPath();

        foreach ($this->readDirectory($this->usersPath) as $userId) {
            if ($userId === '.' || $userId === '..' || $userId === '.DS_Store') {
                continue;
            }
            $serializedUser = @file_get_contents($this->usersPath . '/' . $userId);
            if ($serializedUser === false) {
                throw new UserRepositoryException('Could not read file: ' . $this->usersPath . '/' . $userId);
            }
            $unserializedUser = @unserialize($serializedUser);
            if ($unserializedUser === false) {
                throw new UserRepositoryException('Could not unserialize user!');
            }
            $foundUsers[] = $unserializedUser;
        }
        return $foundUsers;
    }

    /**
     * @param string $id
     * @return User|null
     */
    public function findUserById(string $id)
    {
        $allUsers = $this->findAllUsers();

        foreach ($allUsers as $user) {
            if ($user->id() === $id) {
                return $user;
            }
        }
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername(string $username)
    {
        $allUsers = $this->findAllUsers();

        foreach ($allUsers as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
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
     * @return string
     */
    private function filename(User $user): string
    {
        return $filename = $this->usersPath . '/' . $user->id();
    }

    /**
     * @throws ReportFileRepositoryException
     */
    private function ensureUsersPath()
    {
        if (!file_exists($this->usersPath)) {
            if (!mkdir($this->usersPath)) {
                throw new ReportFileRepositoryException("Could not create directory: $this->usersPath");
            }
        }
    }

    /**
     * @param string $path
     * @return array
     * @throws UserRepositoryException
     */
    private function readDirectory(string $path): array
    {
        $files = @scandir($path);
        if ($files === false) {
            throw new UserRepositoryException("Could not read directory: $path");
        }
        return $files;
    }
}
