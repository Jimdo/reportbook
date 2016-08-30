<?php

namespace Jimdo\Reports\Web;

class Request
{
    /** @var string[] */
    private $queryParams;

    /** @var string[] */
    private $formData;

    /**
     * @param string[] $queryParams
     * @param string[] $formData
     */
    public function __construct(array $queryParams, array $formData)
    {
        $this->queryParams = $queryParams;
        $this->formData = $formData;
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getQueryParams(string $key = null, string $default = null)
    {
        if ($key !== null) {
            if (array_key_exists($key, $this->queryParams) === false) {
                return $default;
            }
            return $this->queryParams[$key];
        }
        return $this->queryParams;
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getFormData(string $key = null, string $default = null)
    {
        if ($key !== null) {
            if (array_key_exists($key, $this->formData) === false) {
                return $default;
            }
            return $this->formData[$key];
        }
        return $this->formData;
    }
}
