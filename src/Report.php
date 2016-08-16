<?php

namespace Jimdo\Reports;

class Report
{
    /** @var string */
    private $content;

    /** @var Trainee */
    private $trainee;

    /**
     * @param Trainee $trainee
     * @param string $content
     */
    public function __construct(Trainee $trainee, string $content)
    {
        $this->content = $content;
        $this->trainee = $trainee;
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
     */
    public function edit(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return Trainee
     */
    public function trainee()
    {
        return $this->trainee;
    }
}
