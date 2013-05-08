<?php
class Post
{
    protected $table = 'posts';
    
    /**
     * You can define your own custom boot method.
     *
     * @return void
     **/
    public static function boot()
    {
        parent::boot();
        
        static::creating(function($post) {
            echo 'Creating';
        });
    }
    
    /**
     * You can access the database connection in a static model method with the resolver.
     *
     * @return void
     **/
    static public function doSomething()
    {
        $db = static::resolveConnection();
    }
}

class PostObserver
{
    public function creating($post)
    {
        echo 'Creating';
    }

    public function created($post)
    {
        echo 'Created';
    }

    public function updating($post)
    {
        echo 'Updating';
    }

    public function updated($post)
    {
        echo 'Updated';
    }

    public function deleting($post)
    {
        echo 'Deleting';
    }

    public function deleted($post)
    {
        echo 'Deleted';
    }

    public function saving($post)
    {
        echo 'Saving';
    }

    public function saved($post)
    {
        echo 'Saved';
    }
}

// Setup Capsule.
// See: https://github.com/illuminate/database
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection(array(
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'database',
    'username'  => 'root',
    'password'  => 'password',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
));

// Setup the Eloquent ORM... (optional).
$capsule->bootEloquent();

// Set the event dispatcher used by Eloquent models... (optional).
use Illuminate\Events\Dispatcher;
$capsule->setEventDispatcher(new Dispatcher);

// Set the cache manager instance used by connections... (optionaL).
use Illuminate\Support\Container;
use Illuminate\Cache\CacheManager;
$cache = new CacheManager(new Container);
$cache->driver('apc');
$capsule->setCacheManager($cache);

// Make this Capsule instance available globally via static methods... (optional).
$capsule->setAsGlobal();

// Observe / Forget Post events.
// See: https://github.com/laravel/framework/issues/1339
$observer = new PostObserver;
$capsule->getContainer()->instance('PostObserver', $observer);
Post::observe($observer);
Post::forget('saved');

// Create / Update / Delete a Post model.
$post = new Post;
$post->title = 'Hello, World!';
$post->save();
$post->delete();

// Fill a Post model.
$post = new Post;
$post->fill(array('title' => 'Hello, World!'));

// Get the query log.
$queries = $capsule->connection()->getQueryLog();