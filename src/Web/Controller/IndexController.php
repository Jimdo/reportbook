<?php

namespace Jimdo\Reports\Web\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        header("Location: /user");
    }
}
