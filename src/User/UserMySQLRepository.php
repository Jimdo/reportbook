<?php

namespace Jimdo\Reports\User;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\MySQLSerializer;

class UserMySQLRepository implements UserRepository
{
    /** @var PDO */
    private $dbHandler;

    /** @var Serializer */
    private $serializer;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /** @var string */
    private $table;

    /**
     * @param PDO $dbHandler
     * @param Serializer $serializer
     * @param ApplicationConfig $applicationConfig
     */
    public function __construct(\PDO $dbHandler, MySQLSerializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
        $this->serializer = $serializer;
        $this->dbHandler = $dbHandler;
        $this->table = 'user';
    }

    /**
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     * @throws UserRepositoryException
     * @return User
     */
    public function createUser(
        string $username,
        string $email,
        Role $role,
        string $password
    ): User {
        $user = new User($username, $email, $role, $password, new UserId());

        $this->save($user);

        return $user;
    }

    /**
     * @param User $user
     * @throws UserRepositoryException
     */
    public function save(User $user)
    {
        $sql = "INSERT INTO $this->table (
            id, username, email, password, roleName, roleStatus
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([
            $user->id(),
            $user->username(),
            $user->email(),
            $user->password(),
            $user->roleName(),
            $user->roleStatus()
        ]);
    }

    /**
     * @param User $deleteUser
     * @throws UserRepositoryException
     */
    public function deleteUser(User $deleteUser)
    {
        $sql = "DELETE FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$deleteUser->id()]);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email)
    {
        $sql = "SELECT * FROM $this->table WHERE email = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$email]);

        $user = $sth->fetchAll();

        if (array_key_exists('0', $user)) {
            return $this->serializer->unserializeUser($user[0]);
        }
    }

    /**
     * @return array
     */
    public function findAllUsers(): array
    {
        $users = [];
        foreach ($this->dbHandler->query("SELECT * FROM $this->table")->fetchAll() as $pdoObject) {
                $users[] = $this->serializer->unserializeUser($pdoObject);
        }
        return $users;
    }

    /**
     * @param string $id
     * @return User|null
     */
    public function findUserById(string $id)
    {
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$id]);

        $user = $sth->fetchAll();

        if (array_key_exists('0', $user)) {
            return $this->serializer->unserializeUser($user[0]);
        }
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername(string $username)
    {

    }

    /**
     * @param string $status
     * @return array
     * @throws UserFileRepositoryException
     */
    public function findUsersByStatus(string $status): array
    {

    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool
    {

    }
}
