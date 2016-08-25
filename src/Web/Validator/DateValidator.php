<?php

namespace Jimdo\Reports\Web\Validator;

class DateValidator extends Validator
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value): bool
    {
        $stringValidator = new StringValidator();
        if (!$stringValidator->isValid($value)) {
            $this->errorMessage = "'{$value}' is no string";
            return false;
        }

        list($day, $month, $year) = explode('.', $value, 3);

        if ((int) $day > 31 || (int) $day <= 0) {
            $this->errorMessage = "Day of '{$value}' is not between 1 and 31";
            return false;
        }

        if ((int) $month > 12 || (int) $month <= 0) {
            $this->errorMessage = "Month of '{$value}' is not between 1 and 12";
            return false;
        }

        if ((int) $year < 0) {
            $this->errorMessage = "Year of '{$value}' is before christ";
            return false;
        }

        return true;
    }
}
