<?php

namespace Jimdo\Reports\Profile;

use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;
use Jimdo\Reports\Serializer as Serializer;

class ProfileMongoRepository implements ProfileRepository
{
    /** @var Serializer */
    public $serializer;

    /** @var MongoDB\Client */
    private $client;

    /** @var MongoDB\Database */
    private $reportbook;

    /** @var MongoDB\Collection */
    private $profiles;

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
        $this->profiles = $this->reportbook->profiles;
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
     */
    public function save(Profile $profile)
    {
        if ($this->exists($profile->userId())) {
            $this->deleteProfile($profile);
            $this->profiles->insertOne($this->serializer->serializeProfile($profile));
        } else {
            $this->profiles->insertOne($this->serializer->serializeProfile($profile));
        }
    }

    /**
     * @param Profile $deleteProfile
     * @throws ProfileRepositoryException
     */
    public function deleteProfile(Profile $deleteProfile)
    {
        $this->profiles->deleteOne(['userId' => $deleteProfile->userId()]);
    }

    /**
     * @param string $userId
     * @return Profile|null
     */
    public function findProfileByUserId(string $userId)
    {
        $serializedProfile = $this->profiles->findOne(['userId' => $userId]);

        if ($serializedProfile !== null) {
            return $this->serializer->unserializeProfile($serializedProfile->getArrayCopy());
        }

        return null;
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $userId): bool
    {
        $profile = $this->findProfileByUserId($userId);

        if ($profile === null) {
            return false;
        }
        return true;
    }
}
