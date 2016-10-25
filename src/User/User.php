<?php

namespace Jimdo\Reports\User;

class User
{
    const PASSWORD_LENGTH = 7;

    const ERR_PASSWORD_LENGTH = 8;
    const ERR_PASSWORD_NOT_NEW = 9;
    const ERR_PASSWORD_WRONG = 10;

    /** @var string */
    private $username;

    /** @var string */
    private $email;

    /** @var Role */
    private $role;

    /** @var string */
    private $password;

    /** @var UserId */
    private $userId;

    /**
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     * @param UserId $userId
     */
    public function __construct(
        string $username,
        string $email,
        Role $role,
        string $password,
        UserId $userId
    ) {
        if (strlen($password) < self::PASSWORD_LENGTH) {
            throw new PasswordException(
                'Password should have at least ' . self::PASSWORD_LENGTH . ' characters!' . "\n",
                self::ERR_PASSWORD_LENGTH
            );
        }
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
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
    public function id():string
    {
        return $this->userId->id();
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
     * @return string
     */
    public function roleName(): string
    {
        return $this->role->name();
    }

    /**
     * @return string
     */
    public function roleStatus(): string
    {
        return $this->role->status();
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }

    public function approve()
    {
        $this->role->approve();
    }

    public function disapprove()
    {
        $this->role->disapprove();
    }

    /**
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     */
    public function edit(string $username, string $email, string $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @param string $oldPassword
     * @param string $newPassword
     */
    public function editPassword(string $oldPassword, string $newPassword)
    {
        if ($this->password() === $oldPassword) {
            if ($this->password() !== $newPassword) {
                if (strlen($newPassword) >= self::PASSWORD_LENGTH) {
                    $this->password = $newPassword;
                } else {
                    throw new PasswordException(
                        'Password should have at least ' . self::PASSWORD_LENGTH . ' characters!',
                        self::ERR_PASSWORD_LENGTH
                    );
                }
            } else {
                throw new PasswordException(
                    "The new password must be different as the old one!",
                    self::ERR_PASSWORD_NOT_NEW
                );
            }
        } else {
            throw new PasswordException(
                "The current password is wrong!",
                self::ERR_PASSWORD_WRONG
            );
        }
    }

    /**
     * @param string $newUsername
     */
    public function editUsername(string $newUsername)
    {
        $this->username = $newUsername;
    }

    /**
     * @param string $newEmail
     */
    public function editEmail(string $newEmail)
    {
        $this->email = $newEmail;
    }
}
