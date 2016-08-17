<?php

namespace Jimdo\Reports\Views;

class Report
{
    /** @var \Jimdo\Reports\Report */
    private $report;

    /**
     * @param \Jimdo\Reports\Report $report
     */
    public function __construct(\Jimdo\Reports\Report $report)
    {
        $this->report = $report;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->report->id();
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return $this->report->content();
    }

    /**
     * @return string
     */
    public function traineeId(): string
    {
        return $this->report->traineeId();
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->report->status();
    }
}
