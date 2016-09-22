<?php

namespace Jimdo\Reports\Web;

class Response
{
    /** @var string */
    private $body;

    /**
     * @param string $body
     */
    public function addBody(string $body)
    {
        $this->body = $this->body . $body;
    }

    /**
     * @return string
     */
    public function render()
    {
        ob_start();
        echo $this->body;
        return ob_get_clean();
    }
}
