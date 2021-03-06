<?php

namespace Jimdo\Reports\Web\Validator;

use Jimdo\Reports\ErrorCodeStore;

class DateValidator extends Validator
{
    /**
    * @param mixed $value
    * @return bool
    */
    public function isValid($value): bool
    {
        if (is_object($value)) {
            $value = get_class($value);
            $this->errorMessage = "'$value' is not a date";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_DATE;
            return false;
        }
        if (is_array($value)) {
            $value = 'Array';
            $this->errorMessage = "'$value' is not a date";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_DATE;
            return false;
        }

        if (!is_string($value)) {
            $this->errorMessage = "'$value' is not a date";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_DATE;
            return false;
        }

        $date = explode('.', $value);

        if (count($date) !== 3) {
            $this->errorMessage = "'$value' is not a date";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_DATE;
            return false;
        }

        list($day, $month, $year) = $date;

        $intValidator = new IntegerValidator();
        if (!$intValidator->isValid($year)) {
            $this->errorMessage = "'$value' is not a date";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_DATE;
            return false;
        }

        $day = (int) $day;
        $month = (int) $month;
        $year = (int) $year;

        if ($day > 31 || $day < 1) {
            $this->errorMessage = "'$value' is not a date";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_DATE;
            return false;
        }

        if ($month > 12 || $month < 1) {
            $this->errorMessage = "'$value' is not a date";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_DATE;
            return false;
        }

        if ($year < 0) {
            $this->errorMessage = "'$value' is not a date";
            $this->errorCode = ErrorCodeStore::ERR_VALIDATOR_DATE;
            return false;
        }

        return true;
    }
}
