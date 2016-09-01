<?php

namespace Jimdo\Reports\Web;

class Router
{
    /** @var string */
    private $defaultController = 'index';

    /** @var string */
    private $defaultAction = 'index';

    /** @var Request */
    private $requestObject;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (isset($config['defaultController'])) {
            $this->defaultController = $config['defaultController'];
        }
        if (isset($config['defaultAction'])) {
            $this->defaultAction = $config['defaultAction'];
        }
        if (isset($config['defaultRequestObject'])) {
            $this->requestObject = $config['defaultRequestObject'];
        } else {
            $this->requestObject = new Request($_GET, $_POST, $_SESSION);
        }
    }

    /**
     * @param string $uri
     * @return mixed
     */
    public function dispatch(string $uri)
    {
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        $uriParts = explode('/', trim($uri, '/'));

        $controller = $this->defaultController;
        $action =  $this->defaultAction;

        if (count($uriParts) >= 1) {
            if ($uriParts[0] !== '') {
                $controller = $uriParts[0];
            }
            if (count($uriParts) >= 2) {
                if ($uriParts[1] !== '') {
                    $action = $uriParts[1];
                }
            }
        }

        $controller = $this->createController($controller);
        $action = $action . 'Action';

        return $controller->$action();
    }

    /**
     * @return string
     */
    public function defaultController(): string
    {
        return $this->controllerName($this->defaultController);
    }

    /**
     * @return string
     */
    public function defaultAction(): string
    {
        return $this->defaultAction . 'Action';
    }

    /**
     * @param string $controller
     * @return mixed
     */
    private function createController(string $controller)
    {
        $class = $this->controllerName($controller);
        return new $class($this->requestObject);
    }

    /**
     * @param string $controller
     * @return string
     */
    private function controllerName(string $controller): string
    {
        return $class = __NAMESPACE__ . '\\Controller\\' . ucfirst($controller) . 'Controller';
    }
}
