<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request as Request;
use Jimdo\Reports\Web\View as View;

abstract class Controller
{
    /** @var Request */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
}
