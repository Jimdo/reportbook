<?php

namespace Jimdo\Reports\Web;

class RequestValidator
{
    /** @var array */
    private $request;

    /** @var array */
    private $fields;

    /** @var string[] */
    private $errorMessages;

    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->request = $request;
        $this->fields = [];
        $this->errorMessages = [];
    }

    /**
     * @param string $field
     * @param string $validator
     * @param mixed ... optional configuration params
     */
    public function add(string $field, string $validator, ...$options)
    {
        if (!isset($this->fields[$field])) {
            $this->fields[$field] = [];
        }

        $this->fields[$field][] = $this->createValidator($validator, $options);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $isValid = true;
        foreach ($this->fields as $field => $validators) {
            foreach ($validators as $validator) {
                if (!$validator->isValid($this->request[$field])) {
                    $isValid = false;
                    if (!isset($this->errorMessages[$field])) {
                        $this->errorMessages[$field] = [];
                    }
                    $this->errorMessages[$field][] = $validator->errorMessage();
                }
            }
        }
        return $isValid;
    }

    /**
     * @return string[]
     */
    public function errorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param string $validator
     * @param array $options
     * @return Validator
     */
    private function createValidator(string $validator, array $options = [])
    {
        $class = __NAMESPACE__ . '\Validator\\' . ucfirst($validator) . 'Validator';
        return new $class(...$options);
    }
}
