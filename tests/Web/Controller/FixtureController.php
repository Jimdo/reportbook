<?php

namespace Jimdo\Reports\Web\Controller;

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

    public function defaultAction()
    {
        return 'defaultAction called';
    }
}
