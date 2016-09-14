<?php

namespace Jimdo\Reports\Web;

class View
{
    /** @var string */
    private $view;

    /** @var array */
    private $variables;

    /**
     * @param string $view
     */
    public function __construct(string $view)
    {
        $this->view = $view;
        $this->variables = [];
    }

    /**
     * @return string
     */
    public function render(): string
    {
        ob_start();
        require __DIR__ . "/../../{$this->view}";
        return ob_get_clean();
    }

    /**
     * @param string $key
     */
    public function __set(string $key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        if (isset($this->variables[$key])) {
            return $this->variables[$key];
        }
        return null;
    }
}
