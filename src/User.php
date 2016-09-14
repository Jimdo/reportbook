<?php

namespace Jimdo\Reports;

class User
{
    /** @var string */
    private $forename;

    /** @var string */
    private $surname;

    /** @var string */
    private $username;

    /** @var string */
    private $email;

    /** @var Role */
    private $role;

    /** @var string */
    private $password;

    /** @var string */
    private $id;

    /**
     * @param string $forename
     * @param string $surname
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     */
    public function __construct(string $forename, string $surname, string $username, string $email, Role $role, string $password)
    {
        $this->forename = $forename;
        $this->surname = $surname;
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
        $this->id = uniqid();
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
    public function id():string
    {
        return $this->id;
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
     * @param string $username
     * @param string $email
     * @param Role $role
     * @param string $password
     */
    public function edit(string $forename, string $surname, string $username, string $email, string $password)
    {
        $this->forename = $forename;
        $this->surname = $surname;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }
}
