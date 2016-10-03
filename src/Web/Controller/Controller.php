<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\RequestValidator as RequestValidator;
use Jimdo\Reports\Web\View as View;
use Jimdo\Reports\Web\Response as Response;
use Jimdo\Reports\Web\ApplicationConfig as ApplicationConfig;

abstract class Controller
{
    /** @var Request */
    protected $request;

    /** @var RequestValidator */
    protected $requestValidator;

    /** @var ApplicationConfig */
    protected $appConfig;

    /** @var Response */
    protected $response;

    /** @var Array */
    protected static $sessionExclude = [];

    /**
     * @param Request $request
     */
    public function __construct(
        Request $request,
        RequestValidator $requestValidator,
        ApplicationConfig $appConfig,
        Response $response
    ) {
        $this->request = $request;
        $this->requestValidator = $requestValidator;
        $this->appConfig = $appConfig;
        $this->response = $response;
    }

    /**
     * @param string $action
     * @return bool
     */
    public static function needSession(string $action)
    {
        return !in_array($action, self::$sessionExclude);
    }


    /**
    * @param string $action
    */
    public static function excludeFromSession(string $action)
    {
        self::$sessionExclude[] = $action;
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    protected function queryParams(string $key = null, string $default = null)
    {
         return $this->request->getQueryParams($key, $default);
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    protected function formData(string $key = null, string $default = null)
    {
        return $this->request->getFormData($key, $default);
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    protected function sessionData(string $key = null, string $default = null)
    {
        return $this->request->getSessionData($key, $default);
    }

    /**
     * @param string $path
     */
    protected function redirect(string $path)
    {
        header("Location: $path");
    }

    /**
     * @param string $role
     * @return bool
     */
    protected function isAuthorized(string $role): bool
    {
        if ((!$this->sessionData('authorized') || $this->sessionData('role') !== $role)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $role
     * @return View
     */
    protected function view(string $path): View
    {
        return new View($path);
    }

    /**
     * @param string $field
     * @param string $validator
     */
    protected function addRequestValidation(string $field, string $validator)
    {
        $this->requestValidator->add($field, $validator);
    }

    protected function isRequestValid()
    {
         $request = array_merge(
             $this->request->getFormData(),
             $this->request->getQueryParams()
         );
        return $this->requestValidator->isValid($request);
    }
}
