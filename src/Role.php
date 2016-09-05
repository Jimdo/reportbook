<?php

namespace Jimdo\Reports;

class Role
{
    /** @var */
    private $name;

    /** @var string */
    private $status;

    const STATUS_NOT_APPROVED = 'NOT_APPROVED';

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->status = self::STATUS_NOT_APPROVED;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }
}
