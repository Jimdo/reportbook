<?php

namespace Jimdo\Reports\Web\Validator;

class DateValidator
{
    /** @var mixed */
    private $value;

    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        $this->value = $value;

        if (!is_string($value)) {
            return false;
        }

        $date = explode('.', $value);

        if (count($date) !== 3) {
            return false;
        }

        $day = (int) $date[0];
        $month = (int) $date[1];
        $year = (int) $date[2];


        if ($day > 31 || $day < 1) {
            return false;
        }

        if ($month > 12 || $month < 1) {
            return false;
        }

        if ($year < 0) {
            return false;
        }

        return true;
    }

    /**
    * @return string
    */
    public function errorMessage(): string
    {
        if (is_object($this->value)) {
            $this->value = get_class($this->value);
        }
        return "'$this->value' is not a date";
    }
}
