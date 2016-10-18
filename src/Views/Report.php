<?php

namespace Jimdo\Reports\Views;

class Report
{
    /** @var \Jimdo\Reports\Reportbook\Report */
    private $report;

    /**
     * @param \Jimdo\Reports\Reportbook\Report $report
     */
    public function __construct(\Jimdo\Reports\Reportbook\Report $report)
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

    /**
     * @return string
     */
    public function date(): string
    {
        return $this->report->date();
    }

    /**
     * @return string
     */
    public function calendarWeek(): string
    {
        return $this->report->calendarWeek();
    }
}
