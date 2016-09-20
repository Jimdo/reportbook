<?php

namespace Jimdo\Reports;

class UserMongoRepository implements UserRepository
{
    /** @var serializer */
    public $serializer;

    /** @var MongoDB\Client */
    private $client;

    /** @var MongoDB\Database */
    private $reportBook;

    /** @var MongoDB\Collection */
    private $users;

    /**
     * @param Serializer $serializer
     */
    public function __construct(\MongoDB\Client $client, Serializer $serializer)
    {
        $this->serializer = $serializer;
        $this->client = $client;
        $this->reportBook = $this->client->reportBook;
        $this->users = $this->reportBook->users;
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
    public function createUser(string $forename, string $surname, string $username, string $email, Role $role, string $password): User
    {
        // if ($this->findUserByEmail($email) !== null) {
        //     throw new UserRepositoryException("Email already exists!\n");
        // }

        $user = new User($forename, $surname, $username, $email, $role, $password, uniqid());

        $this->save($user);

        return $user;
    }

    /**
     * @param User $user
     * @throws UserRepositoryException
     */
    public function save(User $user)
    {
        $this->users->insertOne($this->serializer->serializeUser($user));
    }

    /**
     * @param User $deleteUser
     * @throws UserRepositoryException
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
        foreach ( $this->users->find() as $user )
        {
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
     * @throws UserFileRepositoryException
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
