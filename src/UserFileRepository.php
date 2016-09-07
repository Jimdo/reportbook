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
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return User
     */
    public function createUser(string $forename, string $surname, string $email, Role $role, string $password): User
    {
        $user = new User($forename, $surname, $email, $role, $password);
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
     */
    public function deleteUser(User $deleteUser)
    {

    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email)
    {

    }

    /**
     * @param string $surname
     * @return User|null
     */
    public function findUserBySurname(string $surname)
    {

    }

    /**
     * @return array
     */
    public function findAllUsers(): array
    {

    }

    /**
     * @param string $id
     * @return User|null
     */
    public function findUserById(string $id)
    {

    }

    /**
     * @param User $user
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


}
