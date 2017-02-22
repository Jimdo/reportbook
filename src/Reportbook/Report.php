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

    /** @var string */
    private $calendarYear;

    /** @var string */
    private $category;

    /**
     * @param TraineeId $traineeId
     * @param string $content
     * @param string $date
     * @param string $calendarWeek
     * @param string $id
     * @param string $category
     * @param string $status
     */
    public function __construct(
        TraineeId $traineeId,
        string $content,
        string $date,
        string $calendarWeek,
        string $calendarYear,
        string $id,
        string $category,
        string $status = null
    ) {
        $this->content = $content;
        $this->traineeId = $traineeId->id();
        $this->date = $date;
        $this->calendarWeek = $calendarWeek;
        $this->calendarYear = $calendarYear;
        $this->id = $id;
        $this->category = $category;

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
     * @param bool $replaceNewlines
     * @return string
     */
    public function content(bool $replaceNewlines = false): string
    {
        if ($replaceNewlines) {
            return nl2br($this->content);
        } else {
            return $this->content;
        }
    }

    /**
    * @param string $content
    * @param string $date
    * @param string $calendarWeek
    * @param string $category
    */
    public function edit(string $content, string $date, string $calendarWeek, string $calendarYear, string $category)
    {
        $this->content = $content;
        $this->date = $date;
        $this->calendarWeek = $calendarWeek;
        $this->calendarYear = $calendarYear;
        $this->category = $category;

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

    /**
     * @return string
     */
    public function calendarYear(): string
    {
        return $this->calendarYear;
    }

    /**
     * @return string
     */
    public function category(): string
    {
        return $this->category;
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
