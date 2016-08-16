<?php

namespace Jimdo\Reports;

class Trainee
{
    /** @var string */
    private $forename;

    /**
     * @param string $forename
     */
    public function __construct(string $forename)
    {
        $this->forename = $forename;
    }

    /**
     * @return string
     */
    public function forename(): string
    {
        return $this->forename;
    }
}
