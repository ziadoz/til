<?php

// src/Foobar/Controller/FooController.php

namespace Foobar\Controller;

class FooController
{
    public function helloAction($request)
    {
        return "Hello ".($request->get('name') ?: "World");
    }

    public function indexAction($request)
    {
        return "foo index";
    }
}
