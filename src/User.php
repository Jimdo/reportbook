<?php

namespace Jimdo\Reports;

class User
{
    /** @var string */
    private $forename;

    /** @var string */
    private $surname;

    /** @var string */
    private $email;

    /** @var Role */
    private $role;

    /** @var string */
    private $password;

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param Role $role
     * @param string $password
     */
    public function __construct(string $forename, string $surname, string $email, Role $role, string $password)
    {
        $this->forename = $forename;
        $this->surname = $surname;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
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
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return Role
     */
    public function role(): Role
    {
        return $this->role;
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
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param Role $role
     * @param string $password
     */
    public function edit(string $forename, string $surname, string $email, Role $role, string $password)
    {
        $this->forename = $forename;
        $this->surname = $surname;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
    }
}
