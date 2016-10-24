<?php

namespace Jimdo\Reports\Profile;

class ProfileService
{
    private $repository;

    public function __construct(ProfileMongoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createProfile(string $userId, string $forename, string $surname)
    {
        return $this->repository->createProfile($userId, $forename, $surname);
    }
}
