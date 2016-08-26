<?php

namespace Jimdo\Reports\Web;

class RequestValidator
{
    /** @var array */
    private $fields = [];

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
        foreach ($this->fields as $field => $val) {
            $validator = $this->createValidator($val);

            if (!$validator->isValid($request[$field])) {
                return false;
            }
        }
        return true;
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
