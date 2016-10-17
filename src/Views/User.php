<?php

namespace Jimdo\Reports\Views;

class User
{
    /** @var \Jimdo\Reports\User */
    private $user;

    /**
     * @param \Jimdo\Reports\User $user
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
    public function id()
    {
        return $this->user->id();
    }
}
