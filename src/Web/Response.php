<?php

namespace Jimdo\Reports\Web;

class Response
{
    /** @var string */
    private $body;

    /** @var array */
    private $headers = [];

    /**
     * @param string $body
     */
    public function addBody(string $body)
    {
        $this->body = $this->body . $body;
    }

    /**
     * @param string $body
     */
    public function addHeader(string $header)
    {
        $this->headers[] = $header;
    }

    /**
     * @return string
     */
    public function render()
    {
        foreach ($this->headers as $header) {
            header($header);
        }

        return $this->body;
    }
}
