<?php

namespace Jimdo\Reports\Reportbook;

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

    /** @var TraineeId */
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
     * @param string $id
     */
    public function __construct(
        TraineeId $traineeId,
        string $content,
        string $date,
        string $calendarWeek,
        string $id,
        string $status = null
    ) {
        $this->content = $content;
        $this->traineeId = $traineeId->id();
        $this->date = $date;
        $this->calendarWeek = $calendarWeek;
        $this->id = $id;

        if ($status === null) {
            $this->status = self::STATUS_NEW;
        } else {
            $this->status = $status;
        }
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
        if ($this->status === self::STATUS_DISAPPROVED || $this->status === self::STATUS_REVISED) {
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