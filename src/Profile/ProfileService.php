<?php

namespace Jimdo\Reports\Profile;

class ProfileService
{
    /** @var ProfileMongoRepository */
    private $repository;

    /**
     * @param ProfileMongoRepository $repository
     */
    public function __construct(ProfileMongoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $userId
     * @param string $forename
     * @param string $surname
     * @return Profile
     */
    public function createProfile(string $userId, string $forename, string $surname)
    {
        return $this->repository->createProfile($userId, $forename, $surname);
    }

    /**
     * @param string $userId
     * @return Profile
     */
    public function findProfileByUserId(string $userId)
    {
        return $this->repository->findProfileByUserId($userId);
    }
}
