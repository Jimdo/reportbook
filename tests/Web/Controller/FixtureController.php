<?php

namespace Jimdo\Reports\Web\Controller;

use Jimdo\Reports\Web\Request;

class FixtureController extends Controller
{
    public function testAction()
    {
        return 'testAction called';
    }

    public function indexAction()
    {
        return 'indexAction called';
    }

    public function testQueryParams(string $key = null, string $default = null)
    {
        return $this->queryParams($key, $default);
    }

    public function testFormData(string $key = null, string $default = null)
    {
        return $this->formData($key, $default);
    }

    public function testSessionData(string $key = null, string $default = null)
    {
        return $this->sessionData($key, $default);
    }

    public function testIsTrainee()
    {
        return $this->isTrainee();
    }

    public function testIsTrainer()
    {
        return $this->isTrainer();
    }

    public function testExcludeFromSession(string $action)
    {
        return $this->excludeFromSession($action);
    }

    public function testNeedSession(string $action)
    {
        return $this->needSession($action);
    }

    public function testView(string $path)
    {
        return $this->view($path);
    }

    public function testAddRequestValidations()
    {
        $this->addRequestValidation('name', 'string');
        $this->addRequestValidation('age', 'integer');
    }

    public function testIsRequestValid()
    {
        return $this->isRequestValid();
    }

    public function defaultAction()
    {
        return 'defaultAction called';
    }

    public function requestValidator()
    {
        return $this->requestValidator;
    }
}
