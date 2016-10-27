<?php

namespace Jimdo\Reports\Comment;

class Comment
{
    private $reportId;

    private $userId;

    private $date;

    private $content;

    public function __construct(string $reportId, string $userId, string $date, string $content)
    {
        $this->reportId = $reportId;
        $this->userId = $userId;
        $this->date = $date;
        $this->content = $content;
    }

    public function reportId()
    {
        return $this->reportId;
    }

    public function userId()
    {
        return $this->userId;
    }

    public function date()
    {
        return $this->date;
    }

    public function content()
    {
        return $this->content;
    }
}
