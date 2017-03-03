<?php

namespace Jimdo\Reports\Profile;

use Jimdo\Reports\Web\ApplicationConfig;

class ProfileService
{
    /** @var ProfileMongoRepository */
    private $repository;

    /** @var string */
    private $imagePath;

    /**
     * @param ProfileMongoRepository $repository
     * @param string $imagePath
     * @param ApplicationConfig $appConfig
     */
    public function __construct(ProfileMongoRepository $repository, string $imagePath, ApplicationConfig $appConfig)
    {
        $this->repository = $repository;
        $this->imagePath = $imagePath;
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
        $profile = $this->repository->findProfileByUserId($userId);
        if ($profile === null) {
            return null;
        }
        if ($profile->image() === '') {
            $image = file_get_contents($this->imagePath);
            $base64 = base64_encode($image);
            $profile->editImage($base64, 'jpg');
        }
        return $profile;
    }

    /**
     * @param Profile $deleteProfile
     */
    public function deleteProfile(Profile $deleteProfile)
    {
        $this->repository->deleteProfile($deleteProfile);
    }

    /**
     * @param string $userId
     * @param string $forename
     */
    public function editForename(string $userId, string $forename)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editForename($forename);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $surname
     */
    public function editSurname(string $userId, string $surname)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editSurname($surname);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $dateOfBirth
     */
    public function editDateOfBirth(string $userId, string $dateOfBirth)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editDateOfBirth($dateOfBirth);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $school
     */
    public function editSchool(string $userId, string $school)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editSchool($school);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $company
     */
    public function editCompany(string $userId, string $company)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editCompany($company);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $jobTitle
     */
    public function editJobTitle(string $userId, string $jobTitle)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editJobTitle($jobTitle);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $trainingYear
     */
    public function editTrainingYear(string $userId, string $trainingYear)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editTrainingYear($trainingYear);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $startOfTraining
     */
    public function editStartOfTraining(string $userId, string $startOfTraining)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editStartOfTraining($startOfTraining);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $grade
     */
    public function editGrade(string $userId, string $grade)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editGrade($grade);
        $this->repository->save($profile);
    }

    /**
     * @param string $userId
     * @param string $image
     */
    public function editImage(string $userId, string $image, string $type)
    {
        $profile = $this->repository->findProfileByUserId($userId);
        $profile->editImage($image, $type);
        $this->repository->save($profile);
    }
}
