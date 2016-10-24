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

    /**
     * @param string $userId
     * @param string $forename
     * @param string $surname
     */
    public function __construct(string $userId, string $forename, string $surname)
    {
        $this->userId = $userId;
        $this->forename = $forename;
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function userId(): string
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

    /**
     * @param string $newForename
     */
    public function editForename(string $newForename)
    {
        $this->forename = $newForename;
    }

    /**
     * @param string $newSurname
     */
    public function editSurname(string $newSurname)
    {
        $this->surname = $newSurname;
    }

    /**
     * @param string $newDateOfBirth
     */
    public function editDateOfBirth(string $newDateOfBirth)
    {
        $this->dateOfBirth = $newDateOfBirth;
    }

    /**
     * @param string $newSchool
     */
    public function editSchool(string $newSchool)
    {
        $this->school = $newSchool;
    }

    /**
     * @param string $newJobTitle
     */
    public function editJobTitle(string $newJobTitle)
    {
        $this->jobTitle = $newJobTitle;
    }

    /**
     * @param string $newStartOfTraining
     */
    public function editStartOfTraining(string $newStartOfTraining)
    {
        $this->startOfTraining = $newStartOfTraining;
    }

    /**
     * @param string $newTrainingYear
     */
    public function editTrainingYear(string $newTrainingYear)
    {
        $this->trainingYear = $newTrainingYear;
    }

    /**
     * @param string $newCompany
     */
    public function editCompany(string $newCompany)
    {
        $this->company = $newCompany;
    }

    /**
     * @param string $newGrade
     */
    public function editGrade(string $newGrade)
    {
        $this->grade = $newGrade;
    }

    /**
     * @param string $newImage
     */
    public function editImage(string $newImage)
    {
        $this->image = $newImage;
    }
}
