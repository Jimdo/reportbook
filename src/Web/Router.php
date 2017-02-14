<?php

namespace Jimdo\Reports\Web;

use Jimdo\Reports\Web\ApplicationConfig;
use Monolog\Logger;
use Altmetric\MongoSessionHandler;

class Router
{
    /** @var string */
    private $defaultController = 'index';

    /** @var string */
    private $defaultAction = 'index';

    /** @var Request */
    private $defaultRequestObject;

    /** @var Response */
    private $responseObject;

    /** @var ApplicationConfig */
    private $applicationConfig;

    /** @var RequestValidator */
    private $requestValidator;

    /** @var $ignoreList */
    private $ignoreList = [];

    /**
     * @param ApplicationConfig $applicationConfig
     * @param array $config
     */
    public function __construct(ApplicationConfig $applicationConfig, array $config = [])
    {
        $this->applicationConfig = $applicationConfig;

        if (isset($config['defaultController'])) {
            $this->defaultController = $config['defaultController'];
        }
        if (isset($config['defaultAction'])) {
            $this->defaultAction = $config['defaultAction'];
        }
        if (isset($config['defaultRequestObject'])) {
            $this->defaultRequestObject = $config['defaultRequestObject'];
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

        $controllerClass = $this->controllerName($controller);

        if (!class_exists($controllerClass)) {
            throw new ControllerNotFoundException("Could not find controller class for '$controller'!");
        }

        if ($controllerClass::needSession($action)) {
            $this->startSession();
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

        $loader = new \Twig_Loader_Filesystem($applicationConfig->templatePath);
        $twig = new \Twig_Environment($loader);

        if ($this->defaultRequestObject === null) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                $requestObject = new Request($_GET, $_POST, $_SESSION);
            } else {
                $requestObject = new Request($_GET, $_POST, []);
            }
        } else {
            $requestObject = $this->defaultRequestObject;
        }

        return new $class(
            $requestObject,
            $this->requestValidatorObject,
            $applicationConfig,
            $this->responseObject,
            $twig
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

    protected function startSession()
    {
        if (getenv('APPLICATION_ENV') === 'dev' || getenv('APPLICATION_ENV') === 'production') {
            $uri = sprintf(
                'mongodb://%s:%s@%s:%d/%s',
                $this->applicationConfig->mongoUsername,
                $this->applicationConfig->mongoPassword,
                $this->applicationConfig->mongoHost,
                $this->applicationConfig->mongoPort,
                $this->applicationConfig->mongoDatabase
            );

            $client = new \MongoDB\Client($uri);
            $db = $client->selectDatabase($this->applicationConfig->mongoDatabase);

            $handler = new MongoSessionHandler($db->sessions, new Logger('session'));

            session_set_save_handler($handler);

            session_start();
        }
    }
}
