<?php
// Silex Style Controllers
class App extends \Slim\Slim
{
    public function mount($controller)
    {
        if (! is_object($controller)) {
            throw new \InvalidArgumentException('Controller must be an object.');
        }

        if (! method_exists($controller, 'connect')) {
            throw new \BadMethodCallException('Controller must have a connect method.');
        }
        
        return $controller->connect($this);
    }
}

class Controller
{
    protected $app;

    public function __construct()
    {
        $this->app = $app;
    }
}

class HomeController extends Controller
{
    public function connect()
    {
        $this->app->get('/hello/:name', array($this, 'indexAction'))->name('hello');
    }

    public function indexAction($name)
    {
        return $this->app->render('index.php', array('name' => $name));
    }
}

$app = new App;
$app->mount(new HomeController);
$app->run();