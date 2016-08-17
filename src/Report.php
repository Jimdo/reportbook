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

    /** @var string */
    private $traineeId;

    /** @var string */
    private $status;

    /** @var string */
    private $id;

    /**
     * @param string $traineeId
     * @param string $content
     */
    public function __construct(string $traineeId, string $content)
    {
        $this->content = $content;
        $this->traineeId = $traineeId;
        $this->status = self::STATUS_NEW;
        $this->id = uniqid();
    }


    /**
    * @return string
    */
    public function id(): string
    {
        return $this->id;
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
     * @return string
     */
    public function traineeId(): string
    {
        return $this->traineeId;
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
