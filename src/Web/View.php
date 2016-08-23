<?php

namespace Jimdo\Reports\Web;

class View
{
    private $view;

    private $variables;

    public function __construct(string $view)
    {
        $this->view = $view;
        $this->variables = [];
    }

    public function render(): string
    {
        ob_start();
        require $this->view;
        return ob_get_clean();

    }

    public function __set($key, $value)
    {
        $this->variables[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($this->variables[$key])) {
            return $this->variables[$key];
        }
        return null;
    }}
