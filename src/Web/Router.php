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

    /** @var Response */
    private $responseObject;

    /** @var RequestValidator */
    private $requestValidator;

    /** @var $ignoreList */
    private $ignoreList = [];

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
        if (isset($config['defaultRequestValidatorObject'])) {
            $this->requestValidatorObject = $config['defaultRequestValidatorObject'];
        } else {
            $this->requestValidatorObject = new RequestValidator();
        }
        if (isset($config['defaultResponseObject'])) {
            $this->responseObject = $config['defaultResponseObject'];
        } else {
            $this->responseObject = new Response();
        }
    }

    /**
     * @param string $uri
     * @throws ControllerNotFoundException
     * @return mixed
     */
    public function dispatch(string $uri)
    {
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        $uriParts = explode('/', trim($uri, '/'));

        foreach ($this->ignoreList as $path) {
            if ($uriParts[0] === trim($path, '/')) {
                return null;
            }
        }

        $controller = $this->defaultController;
        $action =  $this->defaultAction;

        if (count($uriParts) >= 1) {
            if ($uriParts[0] !== '') {
                $controller = $uriParts[0];
            }

            if (count($uriParts) >= 2) {
                if ($uriParts[1] !== '') {
                    $givenAction = $uriParts[1] . 'Action';
                    $getMethods = get_class_methods($this->createController($controller));

                    if ($getMethods !== null) {
                        $actionFound = false;
                        foreach ($getMethods as $method) {
                            if ($method === $givenAction) {
                                $actionFound = true;
                                $action = $uriParts[1];
                            }
                        }
                        if ($actionFound === false) {
                            throw new ActionNotFoundException("Could not find $givenAction!");
                        }
                    }
                }
            }
        }


        $controller = $this->createController($controller);
        $action = $action . 'Action';
        $actionString = $controller->$action();

        echo $this->responseObject->render();

        return $actionString;
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
     * @throws ControllerNotFoundException
     * @return mixed
     */
    private function createController(string $controller)
    {
        $class = $this->controllerName($controller);

        if (!class_exists($class)) {
            throw new ControllerNotFoundException("Could not find controller class for '$controller'!");
        }

        $applicationConfig = new ApplicationConfig(realpath(__DIR__ . '/../../config.yml'));
        return new $class(
            $this->requestObject,
            $this->requestValidatorObject,
            $applicationConfig,
            $this->responseObject
        );
    }

    /**
     * @param string $controller
     * @return string
     */
    private function controllerName(string $controller): string
    {
        return __NAMESPACE__ . '\\Controller\\' . ucfirst($controller) . 'Controller';
    }

    /**
     * @param string $path
     */
    public function ignorePath(string $path)
    {
        $this->ignoreList[] = '/' . $path;
    }

    /**
     * @return array
     */
    public function ignoreList(): array
    {
        return $this->ignoreList;
    }
}
