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

    /**
     * @param string $forename
     * @param string $surname
     * @param string $email
     */
    public function __construct(string $forename, string $surname, string $email)
    {
        $this->forename = $forename;
        $this->surname = $surname;
        $this->email = $email;
    }

    public function forename()
    {
        return $this->forename;
    }

    public function surname()
    {
        return $this->surname;
    }

    public function email()
    {
        return $this->email;
    }
}
