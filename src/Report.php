<?php

namespace Jimdo\Reports;

class Report
{
    const STATUS_NEW = 'NEW';

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

    /**
     * @return string
     */
    public function status(): string
    {
        return self::STATUS_NEW;
    }

}
