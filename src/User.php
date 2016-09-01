<?php

namespace Jimdo\Reports;

class User
{
    private $forename;

    private $surname;

    public function __construct(string $forename, string $surname)
    {
        $this->forename = $forename;
        $this->surname = $surname;
    }

    public function forename()
    {
        return $this->forename;
    }

    public function surname()
    {
        return $this->surname;
    }
}
