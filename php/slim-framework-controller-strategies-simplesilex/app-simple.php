<?php
// Simple Style Controllers
class HomeController
{
    public function indexAction($name)
    {
        $app = Slim::getInstance();
        return $app->render('index.php', array('name' => $name));
    }
}

$app = new \Slim\Slim;
$app->get('/hello/:name', array('HomeController', 'indexAction'));