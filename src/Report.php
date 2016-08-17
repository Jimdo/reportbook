<?php

namespace Jimdo\Reports;

class Report
{
    const STATUS_NEW = 'NEW';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_DISAPPROVED = 'DISAPPROVED';
    const STATUS_APPROVAL_REQUESTED = 'APPROVAL_REQUESTED';

    /** @var string */
    private $content;

    /** @var Trainee */
    private $trainee;

    /** @var string */
    private $status;

    /**
     * @param Trainee $trainee
     * @param string $content
     */
    public function __construct(Trainee $trainee, string $content)
    {
        $this->content = $content;
        $this->trainee = $trainee;
        $this->status = self::STATUS_NEW;
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function edit(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return Trainee
     */
    public function trainee()
    {
        return $this->trainee;
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

    public function requestApproval()
    {
        $this->status = self::STATUS_APPROVAL_REQUESTED;
    }
}
