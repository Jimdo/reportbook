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

    /** @var string */
    private $role;

    /** @var string */
    private $password;

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param string $role
     * @param string $password
     */
    public function __construct(string $forename, string $surname, string $email, string $role, string $password)
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
    public function forename()
    {
        return $this->forename;
    }

    /**
     * @return string
     */
    public function surname()
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function role()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     * @param string $role
     * @param string $password
     */
    public function edit(string $forename, string $surname, string $email, string $role, string $password)
    {
        $this->forename = $forename;
        $this->surname = $surname;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
    }

}
