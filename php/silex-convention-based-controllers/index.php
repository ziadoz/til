<?php

// web/index.php

require __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/{controllerName}/{actionName}', function ($controllerName, $actionName) use ($app) {
    $controllerName = ucfirst($controllerName);
    $actionName = ucfirst($actionName);

    $class = "Foobar\Controller\\{$controllerName}Controller";
    $method = "{$actionName}Action";

    if (!class_exists($class)) {
        $app->abort(404);
    }
    
    $reflection = new ReflectionClass($class);
    if (!$reflection->hasMethod($method)) {
        $app->abort(404);
    }

    $controller = new $class();
    return $controller->$method($app['request']);
})
->value('controllerName', 'index')
->value('actionName', 'index');

$app->run();
