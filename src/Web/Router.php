<?php

namespace Jimdo\Reports\Web;

class Router
{
    /**
     * @param string $uri
     * @return mixed
     */
    public function dispatch(string $uri)
    {
        list($controller, $action) = explode('/', trim($uri, '/'));

        $controller = $this->createController($controller);
        $action = $action . 'Action';

        return $controller->$action();
    }

    /**
     * @param string $controller
     * @return mixed
     */
    private function createController(string $controller)
    {
        $class = __NAMESPACE__ . '\\Controller\\' . ucfirst($controller) . 'Controller';
        return new $class(new Request($_GET, $_POST));
    }
}
