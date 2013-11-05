<?php
// Function.
function controller($class) {
    $args  = func_get_args();
    $class = array_shift($args);

    return function() use ($class, $args) {
        if (count($args) > 0) {
            $reflection = new ReflectionClass($class);
            return $reflection->newInstanceArgs($args);
        }

        return new $controller;
    };
}

// Controllers.
class FooController 
{
    public function indexAction()
    {
        return 'Foo';
    }
}

class BarController 
{
    public function __construct()
    {
        print_r(func_get_args());
    }
    
    public function indexAction()
    {
        return 'Bar';
    }
}

// Silex.
$app = new Silex\Application;

$app['controller.foo'] = $app->share(controller('FooController'));
$app['controller.bar'] = $app->share(controller('BarController', 'param', 'param'));

$app->get('/foo', 'controller.foo:indexAction');
$app->get('/bar', 'controller.bar:indexAction');

$app->run();