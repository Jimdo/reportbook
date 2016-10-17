<?php

namespace Jimdo\Reports;

class TraineeId
{
    /** @var string */
    private $id;

    public function __construct(string $id = null)
    {
        if ($id === null) {
            $this->id = uniqId();
        } else {
            $this->id = $id;
        }
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }
}
