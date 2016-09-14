<?php

namespace Jimdo\Reports\Web;

class RequestValidator
{
    /** @var array */
    private $fields = [];

    /** @var array */
    private $errorMessages = [];

    /** @var array */
    private $errorCodes = [];

    /**
     * @param string $field
     * @param string $validator
     */
    public function add(string $field, string $validator)
    {
        $this->fields[$field] = $validator;
    }

    /**
     * @param array $request
     * @return bool
     */
    public function isValid(array $request): bool
    {
        foreach ($this->fields() as $field => $val) {
            $validator = $this->createValidator($val);

            if (!$validator->isValid($request[$field])) {
                $this->errorMessages[$field] = $validator->errorMessage();
                $this->errorCodes[$field] = $validator->errorCode();
            }
        }
        return count($this->errorMessages) === 0;
    }

    /**
     * @return array
     */
    public function errorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @return array
     */
    public function errorCodes(): array
    {
        return $this->errorCodes;
    }

    /**
     * @return array
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @param string $validator
     * @return Validator
     */
    private function createValidator(string $validator)
    {
        $class = __NAMESPACE__ . '\\Validator\\' . ucfirst($validator) . 'Validator';
        return new $class();
    }
}
