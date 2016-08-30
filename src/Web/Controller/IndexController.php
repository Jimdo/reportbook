<?php

namespace Jimdo\Reports\Web\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        var_dump($this->queryParams());
        var_dump($this->formData());
    }
}
