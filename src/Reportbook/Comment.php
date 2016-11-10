<?php

namespace Jimdo\Reports\Reportbook;

class Comment
{
    const STATUS_NEW = 'NEW';
    const STATUS_EDITED = 'EDITED';

    /** @var string */
    private $id;

    /** @var string */
    private $reportId;

    /** @var string */
    private $userId;

    /** @var string */
    private $date;

    /** @var string */
    private $content;

    /** @var string */
    private $status;

    /**
     * @param string $reportId
     * @param string $userId
     * @param string $date
     * @param string $content
     */
    public function __construct(string $id, string $reportId, string $userId, string $date, string $content, string $status = self::STATUS_NEW)
    {
        $this->id = $id;
        $this->reportId = $reportId;
        $this->userId = $userId;
        $this->date = $date;
        $this->content = $content;
        $this->status = $status;
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
    public function reportId(): string
    {
        return $this->reportId;
    }

    /**
     * @return string
     */
    public function userId(): string
    {
        return $this->userId;
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
    public function content(): string
    {
        return $this->content;
    }

    /**
    * @return string
    */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * @param string $newContent
     */
    public function editContent(string $newContent)
    {
        $this->content = $newContent;
        $this->status = self::STATUS_EDITED;
    }
}
