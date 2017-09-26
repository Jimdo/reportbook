<?php

namespace Jimdo\Reports\Notification;

class BrowserNotification
{
    const STATUS_NEW = 'NEW';
    const STATUS_SEEN = 'SEEN';

    /** @var string */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $description;

    /** @var string */
    private $userId;

    /** @var string */
    private $reportId;

    /** @var string */
    private $status;

    /** @var int */
    private $time;

    /**
     * @param string $title
     * @param string $description
     * @param string $userId
     * @param string $reportId
     */
    public function __construct(String $title, String $description, String $userId, String $reportId, string $id = null, int $time = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->userId = $userId;
        $this->reportId = $reportId;

        $this->status = self::STATUS_NEW;

        if ($id === null)
        {
            $this->id = uniqid();
        } else {
            $this->id = $id;
        }

        if ($time === null) {
            $this->time = time();
        } else {
            $this->time = $time;
        }
    }

    /**
     * @return string
     */
    public function id(): String
    {
        return $this->id;
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
    public function userId(): String
    {
        return $this->userId;
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