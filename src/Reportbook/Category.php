<?php

namespace Jimdo\Reports\Reportbook;

class Category
{
    const SCHOOL = 'SCHOOL';
    const COMPANY = 'COMPANY';

    /** @var string */
    private $name;

    /**
     * @param $type
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}
