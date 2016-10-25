<?php

namespace Jimdo\Reports\Profile;

interface ProfileRepository
{
    /**
     * @param string $userId
     * @param string $forename
     * @param string $surname
     * @return Profile
     */
    public function createProfile(string $userId, string $forename, string $surname): Profile;

    /**
     * @param Profile $profile
     * @throws ProfileRepositoryException
     */
    public function save(Profile $profile);

    /**
     * @param Profile $deleteProfile
     * @throws ProfileRepositoryException
     */
    public function deleteProfile(Profile $deleteProfile);

    /**
     * @param string $userId
     * @return Profile|null
     */
    public function findProfileByUserId(string $userId);

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool;
}
