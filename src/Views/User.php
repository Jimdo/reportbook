<?php

namespace Jimdo\Reports\Views;

class User
{
    /** @var \Jimdo\Reports\User\User */
    private $user;

    /**
     * @param \Jimdo\Reports\User\User $user
     */
    public function __construct(\Jimdo\Reports\User\User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function forename(): string
    {
        return $this->user->forename();
    }

    /**
     * @return string
     */
    public function surname(): string
    {
        return $this->user->surname();
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->user->email();
    }

    /**
     * @return string
     */
    public function roleName(): string
    {
        return $this->user->roleName();
    }

    /**
     * @return string
     */
    public function roleStatus(): string
    {
        return $this->user->roleStatus();
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->user->password();
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->user->id();
    }

    /**
     * @param string $userId
     * @param string $newPassword
     * @return string
     */
    public function editUsername(string $userId, string $newPassword): string
    {
        return $this->user->editUsername($userId, $newPassword);
    }

    /**
     * @param string $userId
     * @param string $newEmail
     * @return string
     */
    public function editEmail(string $userId, string $newEmail): string
    {
        return $this->user->editEmail($userId, $newEmail);
    }
}
