<?php

namespace Jimdo\Reports;

class Report
{
    const STATUS_NEW = 'NEW';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_DISAPPROVED = 'DISAPPROVED';
    const STATUS_APPROVAL_REQUESTED = 'APPROVAL_REQUESTED';
    const STATUS_EDITED = 'EDITED';
    const STATUS_REVISED = 'REVISED';

    /** @var string */
    private $content;

    /** @var string */
    private $traineeId;

    /** @var string */
    private $status;

    /** @var string */
    private $id;

    /** @var string */
    private $date;

    /** @var string */
    private $calendarWeek;

    /**
     * @param string $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     */
    public function __construct(string $traineeId, string $content, string $date, string $calendarWeek)
    {
        $this->content = $content;
        $this->traineeId = $traineeId;
        $this->status = self::STATUS_NEW;
        $this->date = $date;
        $this->calendarWeek = $calendarWeek;
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
    * @param string $date
    * @param string $calendarWeek
    */
    public function edit(string $content, string $date, string $calendarWeek)
    {
        $this->content = $content;
        $this->date = $date;
        $this->calendarWeek = $calendarWeek;
        if ($this->status === self::STATUS_DISAPPROVED) {
            $this->status = self::STATUS_REVISED;
        } else {
            $this->status = self::STATUS_EDITED;
        }
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

    /**
     * @return string
     */
    public function date(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function calendarWeek(): string
    {
        return $this->calendarWeek;
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
