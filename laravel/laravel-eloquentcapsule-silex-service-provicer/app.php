<?php
$app = new Silex\Application;
$app->register(new CapsuleServiceProvider, array(
    // DB Connection: Multiple.
    'capsule.connections' => array(
        'default' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'dname1',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'logging'   => false, // Toggle query logging on this connection.
        ),

        'other' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'dbname2',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'logging'   => true,  // Toggle query logging on this connection.
        ),
    ),

    /*
    // DB Connection: Single.
    'capsule.connection' => array(
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'dbname',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
        'logging'   => true, // Toggle query logging on this connection.
    ),
    */

    // Cache.
    'capsule.cache' => array(
        'driver' => 'apc',
        'prefix' => 'laravel',
    ),
    
    /*
    // Cache: Available Options.
    'capsule.cache' => array(
        'driver'        => 'file',
        'path'          => '/path/to/cache',
        'connection'    => null,
        'table'         => 'cache',

        'memcached' => array(
            array(
                'host'      => '127.0.0.1',
                'port'      => 11211,
                'weight'    => 100
            ),
        ),

        'prefix' => 'laravel',
    ),
    */
    
    /*
    Other Options:
    'capsule.global'   => true, // Enable global access to Capsule query builder.
    'capsule.eloquent' => true, // Automatically boot Eloquent ORM.
    */
));

$app['capsule']; // Establish database connection manually (otherwise this occurs upon $app->run()).

// Create an Eloquent Model.
class Book extends Illuminate\Database\Eloquent\Model
{
    protected $table = 'books';
}

// Work with the Eloquent Model.
$book = new Book;
$book->title = '61 Hours';
$book->author = 'Lee Child';
$book->save();

$book = Book::find(1);
print_r($book);

// Use the Capsule query builder globally.
use Illuminate\Database\Capsule\Manager as Capsule;
$book = Capsule::table('books')->where('id', 1)->get();
print_r($book);

$app->run(); // Database connection established automatically upon Silex run.