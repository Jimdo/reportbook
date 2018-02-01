<?php

namespace Jimdo\Reports\User;

use Jimdo\Reports\Web\ApplicationConfig;
use Jimdo\Reports\MySQLSerializer;
use Jimdo\Reports\ErrorCodeStore;

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
        if ($this->findUserByUsername($username) !== null) {
            throw new UserRepositoryException(
                "Username already exists!\n",
                ErrorCodeStore::ERR_USERNAME_EXISTS
            );
        }

        if ($this->findUserByEmail($email) !== null) {
            throw new UserRepositoryException(
                "Email already exists!\n",
                ErrorCodeStore::ERR_EMAIL_EXISTS
            );
        }

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
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$user->id()]);
        $foundUser = $sth->fetchAll();

        if (array_key_exists('0', $foundUser)) {
            $sql = "UPDATE $this->table SET id=:userId, username=:username, email=:email, password=:password, roleName=:roleName, roleStatus=:roleStatus
                    WHERE id = :userId";
        } else {
            $sql = "INSERT INTO $this->table (
                id, username, email, password, roleName, roleStatus
            ) VALUES (
                :userId, :username, :email, :password, :roleName, :roleStatus
            )";
        }
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([
            ':userId' => $user->id(),
            ':username' => $user->username(),
            ':email' => $user->email(),
            ':password' => $user->password(),
            ':roleName' => $user->roleName(),
            ':roleStatus' => $user->roleStatus()
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

        if ($this->checkIfUserFound($user)) {
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

        if ($this->checkIfUserFound($user)) {
            return $this->serializer->unserializeUser($user[0]);
        }
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername(string $username)
    {
        $sql = "SELECT * FROM $this->table WHERE username = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$username]);

        $user = $sth->fetchAll();

        if ($this->checkIfUserFound($user)) {
            return $this->serializer->unserializeUser($user[0]);
        }
    }

    /**
     * @param string $status
     * @return array
     */
    public function findUsersByStatus(string $status): array
    {
        $sql = "SELECT * FROM $this->table WHERE roleStatus = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$status]);

        $users = [];
        foreach ($sth->fetchAll() as $userArr) {
                $users[] = $this->serializer->unserializeUser($userArr);
        }
        return $users;
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool
    {
        $sql = "SELECT * FROM $this->table WHERE username = ? OR email = ?";
        $sth = $this->dbHandler->prepare($sql);

        $sth->execute([
            $identifier,
            $identifier
        ]);

        if ($this->checkIfUserFound($sth->fetchAll())) {
            return true;
        }
        return false;
    }

    /**
     * @param array $userArr
     * @return bool
     */
    private function checkIfUserFound(array $userArr): bool
    {
        if (array_key_exists('0', $userArr)) {
            return true;
        }
        return false;
    }
}
