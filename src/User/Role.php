<?php

namespace Jimdo\Reports\User;

class Role
{
    /** @var string */
    private $name;

    /** @var string */
    private $status;

    const TRAINEE = 'TRAINEE';
    const TRAINER = 'TRAINER';
    const ADMIN = 'ADMIN';
    const STATUS_NOT_APPROVED = 'NOT_APPROVED';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_DISAPPROVED = 'DISAPPROVED';

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        if ($name === self::ADMIN) {
            $this->status = self::STATUS_APPROVED;
        } else {
            $this->status = self::STATUS_NOT_APPROVED;
        }
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

    public function approve()
    {
        $this->status = self::STATUS_APPROVED;
    }

    public function disapprove()
    {
        $this->status = self::STATUS_DISAPPROVED;
    }
}
