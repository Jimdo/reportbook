<?php

namespace Jimdo\Reports\Profile;

use Jimdo\Reports\User\UserId as UserId;

class Profile
{
    /** @var string */
    private $forename;

    /** @var string */
    private $surname;

    /** @var UserId */
    private $userId;

    /** @var string */
    private $dateOfBirth = '';

    /** @var string */
    private $school = '';

    /** @var string */
    private $grade = '';

    /** @var string */
    private $jobTitle = '';

    /** @var string */
    private $trainingYear = '';

    /** @var string */
    private $company = '';

    /** @var string */
    private $startOfTraining = '';

    /** @var string */
    private $image = '';


    public function __construct(string $userId, string $forename, string $surname)
    {
        $this->userId = $userId;
        $this->forename = $forename;
        $this->surname = $surname;
    }

    public function userId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function forename(): string
    {
        return $this->forename;
    }

    /**
     * @return string
     */
    public function surname(): string
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function dateOfBirth(): string
    {
        return $this->dateOfBirth;
    }

    /**
     * @return string
     */
    public function school():string
    {
        return $this->school;
    }

    /**
     * @return string
     */
    public function company():string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function jobTitle():string
    {
        return $this->jobTitle;
    }

    /**
     * @return string
     */
    public function startOfTraining():string
    {
        return $this->startOfTraining;
    }

    /**
     * @return string
     */
    public function trainingYear():string
    {
        return $this->trainingYear;
    }

    /**
     * @return string
     */
    public function grade():string
    {
        return $this->grade;
    }

    /**
     * @return string
     */
    public function image():string
    {
        return $this->image;
    }

}
