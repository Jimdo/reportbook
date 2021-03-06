<?php

namespace Jimdo\Reports\Reportbook;

class TraineeId
{
    /** @var string */
    private $id;

    /**
     * @param string $id
     */
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
