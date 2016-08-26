<?php

namespace Jimdo\Reports\Web;

class RequestValidator
{
    private $fields = [];

    public function add(string $field, string $validator)
    {
        $this->fields[$field] = $validator;
    }

    public function isValid(array $request)
    {
        foreach ($this->fields as $field => $val) {
            $validator = $this->createValidator($val);

            if (!$validator->isValid($request[$field])) {
                return false;
            }
        }
        return true;
    }

    private function createValidator(string $validator)
    {
        $class = __NAMESPACE__ . '\\Validator\\' . ucfirst($validator) . 'Validator';
        return new $class();
    }
}
