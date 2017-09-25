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
    private $status;

    /**
     * @param string $title
     * @param string $description
     */
    public function __construct(String $title, String $description)
    {
        $this->title = $title;
        $this->description = $description;

        $this->status = self::STATUS_NEW;
    }

    public function title(): String
    {
        return $this->title;
    }

    public function description(): String
    {
        return $this->description;
    }

    public function status(): String
    {
        return $this->status;
    }
}