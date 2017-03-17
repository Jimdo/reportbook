<?php

namespace Jimdo\Reports\Profile;

use Jimdo\Reports\MySQLSerializer;
use Jimdo\Reports\Web\ApplicationConfig;

class ProfileMySQLRepository implements ProfileRepository
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
        $this->table = 'profile';
    }

    /**
     * @param string $userId
     * @param string $forename
     * @param string $surname
     * @return Profile
     */
    public function createProfile(string $userId, string $forename, string $surname): Profile
    {
        $profile = new Profile($userId, $forename, $surname);
        $this->save($profile);
        return $profile;
    }

    /**
     * @param Profile $profile
     * @throws ProfileRepositoryException
     */
    public function save(Profile $profile)
    {
        $sql = "INSERT INTO $this->table (
            userId, forename, surname, dateOfBirth, school, grade, jobTitle, trainingYear, company, startOfTraining, image, imageType
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([
            $profile->userId(),
            $profile->forename(),
            $profile->surname(),
            $profile->dateOfBirth(),
            $profile->school(),
            $profile->grade(),
            $profile->jobTitle(),
            $profile->trainingYear(),
            $profile->company(),
            $profile->startOfTraining(),
            $profile->image(),
            $profile->imageType()
        ]);
    }

    /**
     * @param Profile $deleteProfile
     * @throws ProfileRepositoryException
     */
    public function deleteProfile(Profile $deleteProfile)
    {
        $sql = "DELETE FROM $this->table WHERE userId = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$deleteProfile->userId()]);
    }
    /**
     * @param string $userId
     * @return Profile|null
     */
    public function findProfileByUserId(string $userId)
    {
        $sql = "SELECT * FROM $this->table WHERE userId = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$userId]);

        $profile = $sth->fetchAll();

        if (array_key_exists('0', $profile)) {
            return $this->serializer->unserializeProfile($profile[0]);
        }
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier): bool
    {
        $sql = "SELECT * FROM $this->table WHERE userId = ?";
        $sth = $this->dbHandler->prepare($sql);
        $sth->execute([$identifier]);

        $profile = $sth->fetchAll();

        if (array_key_exists('0', $profile)) {
            return true;
        }
        return false;
    }
}
