<?php

namespace Jimdo\Reports;

class Report
{
    /** @var string */
    private $content;

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }
}
