<?php

namespace Jimdo\Reports\User;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class UserMongoRepository implements UserRepository
{
    /** @var Serializer */
    public $serializer;

    /** @var MongoDB\Client */
    private $client;

    /** @var MongoDB\Database */
    private $reportbook;

    /** @var MongoDB\Collection */
    private $users;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /**
     * @param Serializer $serializer
     * @param Client $client
     */
    public function __construct(\MongoDB\Client $client, Serializer $serializer, ApplicationConfig $applicationConfig)
    {
        $this->applicationConfig = new ApplicationConfig(__DIR__ . '/../../config.yml');
        $this->serializer = $serializer;
        $this->client = $client;
        $this->reportbook = $this->client->selectDatabase($this->applicationConfig->mongoDatabase);
        $this->users = $this->reportbook->users;
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

        $this->save($user);

        return $user;
    }

    /**
     * @param User $user
     */
    public function save(User $user, string $identifier = null)
    {
        $id = $user->username();

        if ($identifier !== null) {
            $id = $identifier;
        }

        if ($this->exists($id)) {
            $this->deleteUser($user);
            $this->users->insertOne($this->serializer->serializeUser($user));
        } else {
            $this->users->insertOne($this->serializer->serializeUser($user));
        }
    }

    /**
     * @param User $deleteUser
     */
    public function deleteUser(User $deleteUser)
    {
        $this->users->deleteOne(['id' => $deleteUser->id()]);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email)
    {
        $serializedUser = $this->users->findOne(['email' => $email]);

        if ($serializedUser !== null) {
            return $this->serializer->unserializeUser($serializedUser->getArrayCopy());
        }

        return null;
    }

    /**
     * @param string $surname
     * @return User|null
     */
    public function findUserBySurname(string $surname)
    {
        $serializedUser = $this->users->findOne(['surname' => $surname]);

        if ($serializedUser !== null) {
            return $this->serializer->unserializeUser($serializedUser->getArrayCopy());
        }

        return null;
    }

    /**
     * @return array
     */
    public function findAllUsers(): array
    {
        $foundUsers = [];
        foreach ($this->users->find() as $user) {
            $foundUsers[] = $this->serializer->unserializeUser($user->getArrayCopy());
        }
        return $foundUsers;
    }

    /**
     * @param string $id
     * @return User|null
     */
    public function findUserById(string $id)
    {
        $serializedUser = $this->users->findOne(['id' => $id]);

        if ($serializedUser !== null) {
            return $this->serializer->unserializeUser($serializedUser->getArrayCopy());
        }

        return null;
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername(string $username)
    {
        $serializedUser = $this->users->findOne(['username' => $username]);

        if ($serializedUser !== null) {
            return $this->serializer->unserializeUser($serializedUser->getArrayCopy());
        }

        return null;
    }

    /**
     * @param string $status
     * @return array
     */
    public function findUsersByStatus(string $status): array
    {
        $foundUsers = $this->findAllUsers();
        $users = [];

        foreach ($foundUsers as $user) {
            if ($user->roleStatus() === $status) {
                $users[] = $user;
            }
        }
        return $users;
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
}
