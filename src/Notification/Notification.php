<?php

namespace Jimdo\Reports\Notification;

class Notification
{
    const STATUS_NEW = 'NEW';
    const STATUS_SEEN = 'SEEN';

    /** @var string */
    private $title;

    /** @var string */
    private $description;

    /** @var string */
    private $reportId;

    /** @var string */
    private $status;

    /** @var int */
    private $time;

    /**
     * @param string $title
     * @param string $description
     * @param string $reportId
     */
    public function __construct(String $title, String $description, String $reportId)
    {
        $this->title = $title;
        $this->description = $description;
        $this->reportId = $reportId;

        $this->status = self::STATUS_NEW;
        $this->time = time();
    }

    /**
     * @return string
     */
    public function title(): String
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function description(): String
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function reportId(): String
    {
        return $this->reportId;
    }

    /**
     * @return string
     */
    public function status(): String
    {
        return $this->status;
    }

    /**
    * @return int
    */
    public function time(): int
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function seen()
    {
        $this->status = self::STATUS_SEEN;
    }
}