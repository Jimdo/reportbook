<?php

namespace Jimdo\Reports\Web;

class FixtureController extends Controller
{
    public function testAction()
    {
        return 'testAction called';
    }

    public function testQueryParams(string $key = null, string $default = null)
    {
        return $this->queryParams($key, $default);
    }

    public function testFormData(string $key = null, string $default = null)
    {
        return $this->formData($key, $default);
    }
}
