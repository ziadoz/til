# Laravel's Dependency Injection Container in Depth

***Translations:** [Korean](https://blog.pas3ive.com/php/2018/07/04/laravel-dependency-injection-container.html) (by Yongwoo Lee)*

[Laravel](https://laravel.com/) has a powerful Inversion of Control (IoC) / Dependency Injection (DI) Container. Unfortunately the [official documentation](https://laravel.com/docs/5.4/container) doesn't cover all of the available functionality, so I decided to experiment with it and document it for myself. The following is based on [Laravel 5.4.26](https://github.com/laravel/framework/tree/5.4/src/Illuminate/Container) - other versions may vary.

## Introduction to Dependency Injection

I won't attempt to explain the principles behind DI / IoC here - if you're not familiar with them you might want to read [What is Dependency Injection?](http://fabien.potencier.org/what-is-dependency-injection.html) by Fabien Potencier (creator of the [Symfony](http://symfony.com/) framework).

## Accessing the Container

There are several ways to access the Container instance* within Laravel, but the simplest is to call the `app()` helper method:

```php
$container = app();
```

I won't describe the other ways today - instead I want to focus on the Container class itself.

**Note:** If you read the [official docs](https://laravel.com/docs/5.4/container), it uses `$this->app` instead of `$container`.

(* In Laravel applications it's actually a subclass of Container called [Application](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Foundation/Application.php) (which is why the helper is called `app()`), but for this post I'll only describe [Container](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Container/Container.php) methods.)

### Using Illuminate\Container Outside Laravel

To use Container outside of Laravel, [install it](https://packagist.org/packages/illuminate/container) and then:

```php
use Illuminate\Container\Container;

$container = Container::getInstance();
```

## Basic Usage

The simplest usage is to type hint your class's constructor with the classes you want injected:

```php
class MyClass
{
    private $dependency;

    public function __construct(AnotherClass $dependency)
    {
        $this->dependency = $dependency;
    }
}
```

Then instead of using `new MyClass`, use the Container's `make()` method:

```php
$instance = $container->make(MyClass::class);
```

The container will automatically instantiate the dependencies, so this is functionally equivalent to:

```php
$instance = new MyClass(new AnotherClass());
```

(Except `AnotherClass` could have some dependencies of its own - in which case Container would recursively instantiate them until there were no more.)

### Practical Example

Here's a more practical example based on the [PHP-DI docs](http://php-di.org/doc/getting-started.html) - separating the mailer functionality from the user registration:

```php
class Mailer
{
    public function mail($recipient, $content)
    {
        // Send an email to the recipient
        // ...
    }
}
```

```php
class UserManager
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register($email, $password)
    {
        // Create the user account
        // ...

        // Send the user an email to say hello!
        $this->mailer->mail($email, 'Hello and welcome!');
    }
}
```

```php
use Illuminate\Container\Container;

$container = Container::getInstance();

$userManager = $container->make(UserManager::class);
$userManager->register('dave@davejamesmiller.com', 'MySuperSecurePassword!');
```

## Binding Interfaces to Implementations

The Container makes it easy to code to an interface and then instantiate a concrete instance at runtime. First define the interfaces:

```php
interface MyInterface { /* ... */ }
interface AnotherInterface { /* ... */ }
```

And declare the concrete classes implementing those interfaces. They may depend on other interfaces (or concrete classes as before):

```php
class MyClass implements MyInterface
{
    private $dependency;

    public function __construct(AnotherInterface $dependency)
    {
        $this->dependency = $dependency;
    }
}
```

Then use `bind()` to map each interface to a concrete class:

```php
$container->bind(MyInterface::class, MyClass::class);
$container->bind(AnotherInterface::class, AnotherClass::class);
```

Finally pass the interface name instead of the class name to `make()`:

```php
$instance = $container->make(MyInterface::class);
```

**Note:** If you forget to bind an interface you will get a slightly cryptic fatal error instead:

```
Fatal error: Uncaught ReflectionException: Class MyInterface does not exist
```

This is because the container will try to instantiate the interface (`new MyInterface`), which isn't a valid class.

### Practical Example

Here's a practical example of this - a swappable cache layer:

```php
interface Cache
{
    public function get($key);
    public function put($key, $value);
}
```

```php
class RedisCache implements Cache
{
    public function get($key) { /* ... */ }
    public function put($key, $value) { /* ... */ }
}
```

```php
class Worker
{
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function result()
    {
        // Use the cache for something...
        $result = $this->cache->get('worker');

        if ($result === null) {
            $result = do_something_slow();

            $this->cache->put('worker', $result);
        }

        return $result;
    }
}
```

```php
use Illuminate\Container\Container;

$container = Container::getInstance();
$container->bind(Cache::class, RedisCache::class);

$result = $container->make(Worker::class)->result();
```

## Binding Abstract & Concrete Classes

Binding can be also used with abstract classes:

```php
$container->bind(MyAbstract::class, MyConcreteClass::class);
```

Or to replace a concrete class with a subclass:

```php
$container->bind(MySQLDatabase::class, CustomMySQLDatabase::class);
```

## Custom Bindings

If the class requires additional configuration you can pass a closure instead of a class name as the second parameter to `bind()`:

```php
$container->bind(Database::class, function (Container $container) {
    return new MySQLDatabase(MYSQL_HOST, MYSQL_PORT, MYSQL_USER, MYSQL_PASS);
});
```

Each time the Database interface is required, a new MySQLDatabase instance will be created and used, with the specified configuration values. (To share a single instance, see _Singletons_ below.) The closure receives the Container instance as the first parameter, and it can be used to instantiate other classes if needed:

```php
$container->bind(Logger::class, function (Container $container) {
    $filesystem = $container->make(Filesystem::class);

    return new FileLogger($filesystem, 'logs/error.log');
});
```

A closure can also be used to customise how a concrete class is instantiated:

```php
$container->bind(GitHub\Client::class, function (Container $container) {
    $client = new GitHub\Client;
    $client->setEnterpriseUrl(GITHUB_HOST);
    return $client;
});
```

### Resolving Callbacks

Instead of overriding the binding completely, you can use `resolving()` to register a callback that's called after the binding is revolved:

```php
$container->resolving(GitHub\Client::class, function ($client, Container $container) {
    $client->setEnterpriseUrl(GITHUB_HOST);
});
```

If there are multiple callbacks, they will all be called. They also work for interfaces and abstract classes:

```php
$container->resolving(Logger::class, function (Logger $logger) {
    $logger->setLevel('debug');
});

$container->resolving(FileLogger::class, function (FileLogger $logger) {
    $logger->setFilename('logs/debug.log');
});

$container->bind(Logger::class, FileLogger::class);

$logger = $container->make(Logger::class);
```

It is also possible to add a callback that's always called no matter what class is resolved - but I think it's probably only useful for logging / debugging:

```php
$container->resolving(function ($object, Container $container) {
    // ...
});
```

### Extending a Class

Alternatively you can also use `extend()` to wrap a class and return a different object:

```php
$container->extend(APIClient::class, function ($client, Container $container) {
    return new APIClientDecorator($client);
});
```

The resulting object should still implement the same interface though, otherwise you'll get an error when using type hinting.

## Singletons

With both automatic binding and `bind()`, a new instance will be created (or the closure will be called) every time it's needed. To share a single instance, use `singleton()` instead of `bind()`:

```php
$container->singleton(Cache::class, RedisCache::class);
```

Or with a closure:

```php
$container->singleton(Database::class, function (Container $container) {
    return new MySQLDatabase('localhost', 'testdb', 'user', 'pass');
});
```

To make a concrete class a singleton, pass that class with no second parameter:

```php
$container->singleton(MySQLDatabase::class);
```

In each case, the singleton object will be created the first time it is needed, and then reused each subsequent time. If you already have an instance that you want to reuse, use the `instance()` method instead. For example, Laravel uses this to make sure the singleton Container instance is returned whenever it is injected into a class:

```php
$container->instance(Container::class, $container);
```

## Arbitrary Binding Names

You can use any arbitrary string instead of a class/interface name - although you won't be able to use type hinting to retrieve it and will have to use `make()` instead:

```php
$container->bind('database', MySQLDatabase::class);

$db = $container->make('database');
```

To support both a class/interface and a short name simultaneously, use `alias()`:

```php
$container->singleton(Cache::class, RedisCache::class);
$container->alias(Cache::class, 'cache');

$cache1 = $container->make(Cache::class);
$cache2 = $container->make('cache');

assert($cache1 === $cache2);
```

## Storing Arbitrary Values

You can also use the container to store arbitrary values - e.g. configuration data:

```php
$container->instance('database.name', 'testdb');

$db_name = $container->make('database.name');
```

It supports array access syntax, which makes this feel more natural:

```php
$container['database.name'] = 'testdb';

$db_name = $container['database.name'];
```

When combined with closure bindings you can see why this could be useful:

```php
$container->singleton('database', function (Container $container) {
    return new MySQLDatabase(
        $container['database.host'],
        $container['database.name'],
        $container['database.user'],
        $container['database.pass']
    );
});
```

(Laravel itself doesn't use the container for configuration - it uses a separate [Config](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Config/Repository.php) class instead - but [PHP-DI](http://php-di.org/doc/php-definitions.html#values) does.)

**Tip:** Array syntax can also be used instead of `make()` when instantiating objects:

```php
$db = $container['database'];
```

## Dependency Injection for Functions & Methods

So far we've seen DI for constructors, but Laravel also supports DI for arbitrary functions:

```php
function do_something(Cache $cache) { /* ... */ }

$result = $container->call('do_something');
```

Additional parameters can be passed as an ordered or associative array:

```php
function show_product(Cache $cache, $id, $tab = 'details') { /* ... */ }

// show_product($cache, 1)
$container->call('show_product', [1]);
$container->call('show_product', ['id' => 1]);

// show_product($cache, 1, 'spec')
$container->call('show_product', [1, 'spec']);
$container->call('show_product', ['id' => 1, 'tab' => 'spec']);
```

This can be used for any callable method:

#### Closures

```php
$closure = function (Cache $cache) { /* ... */ };

$container->call($closure);
```

#### Static methods

```php
class SomeClass
{
    public static function staticMethod(Cache $cache) { /* ... */ }
}

```

```php
$container->call(['SomeClass', 'staticMethod']);
// or:
$container->call('SomeClass::staticMethod');
```

#### Instance methods

```php
class PostController
{
    public function index(Cache $cache) { /* ... */ }
    public function show(Cache $cache, $id) { /* ... */ }
}
```

```php
$controller = $container->make(PostController::class);

$container->call([$controller, 'index']);
$container->call([$controller, 'show'], ['id' => 1]);
```

### Shortcut for Calling Instance Methods

There is a shortcut to instantiate a class and call a method in one go - use the syntax `ClassName@methodName`:

```php
$container->call('PostController@index');
$container->call('PostController@show', ['id' => 4]);
```

The container is used to instantiate the class. This means:

1. Dependencies are injected into the constructor (as well as the method).
2. You can define the class as a singleton if you want it to be reused.
3. You can use an interface or arbitrary name instead of a concrete class.

For example, this will work:

```php
class PostController
{
    public function __construct(Request $request) { /* ... */ }
    public function index(Cache $cache) { /* ... */ }
}
```

```php
$container->singleton('post', PostController::class);
$container->call('post@index');
```

Finally, you can pass a "default method" as the third parameter. If the first parameter is a class name with no method specified, the default method will be called instead. Laravel uses this to implement [event handlers](https://laravel.com/docs/5.4/events#registering-events-and-listeners):

```php
$container->call(MyEventHandler::class, $parameters, 'handle');

// Equivalent to:
$container->call('MyEventHandler@handle', $parameters);
```

### Method Call Bindings

The `bindMethod()` method can be used to override a method call, e.g. to pass additional parameters:

```php
$container->bindMethod('PostController@index', function ($controller, $container) {
    $posts = get_posts(...);

    return $controller->index($posts);
});
```

All of these will work, calling the closure instead of the original method:

```php
$container->call('PostController@index');
$container->call('PostController', [], 'index');
$container->call([new PostController, 'index']);
```

However, any additional parameters to `call()` are not passed into the closure so they can't be used.

```php
$container->call('PostController@index', ['Not used :-(']);
```

_**Notes:** This method is not part of the [Container interface](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Contracts/Container/Container.php), only the concrete [Container class](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Container/Container.php). See [the PR where it was added](https://github.com/laravel/framework/pull/16800) for why parameters are ignored._

## Contextual Bindings

Sometimes you want to use different implementations of an interface in different places. Here is an example adapted from the [Laravel docs](https://laravel.com/docs/5.4/container#contextual-binding):

```php
$container
    ->when(PhotoController::class)
    ->needs(Filesystem::class)
    ->give(LocalFilesystem::class);

$container
    ->when(VideoController::class)
    ->needs(Filesystem::class)
    ->give(S3Filesystem::class);
```

Now both PhotoController and VideoController can depend on the Filesystem interface, yet each will receive a different implementation. You can also use a closure for `give()`, just as you can with `bind()`:

```php
$container
    ->when(VideoController::class)
    ->needs(Filesystem::class)
    ->give(function () {
        return Storage::disk('s3');
    });
```

Or a named dependency:

```php
$container->instance('s3', $s3Filesystem);

$container
    ->when(VideoController::class)
    ->needs(Filesystem::class)
    ->give('s3');
```

### Binding Parameters to Primitives

You can also bind primitives (strings, integers, etc.) by passing a variable name to `needs()` (instead of an interface) and passing the value to `give()`:

```php
$container
    ->when(MySQLDatabase::class)
    ->needs('$username')
    ->give(DB_USER);
```

You can use a closure to delay retrieving the value until it is needed:

```php
$container
    ->when(MySQLDatabase::class)
    ->needs('$username')
    ->give(function () {
        return config('database.user');
    });
```

Here you can't pass a class or a named dependency (e.g. `give('database.user')`) because it would be returned as a literal value - to do that you would have to use a closure instead:

```php
$container
    ->when(MySQLDatabase::class)
    ->needs('$username')
    ->give(function (Container $container) {
        return $container['database.user'];
    });
```

## Tagging

You can use the container to "tag" related bindings:

```php
$container->tag(MyPlugin::class, 'plugin');
$container->tag(AnotherPlugin::class, 'plugin');
```

And then retrieve all tagged instances as an array:

```php
foreach ($container->tagged('plugin') as $plugin) {
    $plugin->init();
}
```

Both `tag()` parameters also accept arrays:

```php
$container->tag([MyPlugin::class, AnotherPlugin::class], 'plugin');
$container->tag(MyPlugin::class, ['plugin', 'plugin.admin']);
```

## Rebinding

_**Note:** This is a little more advanced, and only rarely needed - feel free to skip over it!_

A `rebinding()` callback is called when a binding or instance is changed after it has already been used - for example, here the session class is replaced after it has been used by the Auth class, so the Auth class needs to be informed of the change:

```php
$container->singleton(Auth::class, function (Container $container) {
    $auth = new Auth;
    $auth->setSession($container->make(Session::class));

    $container->rebinding(Session::class, function ($container, $session) use ($auth) {
        $auth->setSession($session);
    });

    return $auth;
});

$container->instance(Session::class, new Session(['username' => 'dave']));
$auth = $container->make(Auth::class);
echo $auth->username(); // dave
$container->instance(Session::class, new Session(['username' => 'danny']));

echo $auth->username(); // danny
```

(For more information about rebinding, see [here](https://stackoverflow.com/questions/38974593/laravels-ioc-container-rebinding-abstract-types) and [here](https://code.tutsplus.com/tutorials/digging-in-to-laravels-ioc-container--cms-22167).)

### refresh()

There is also a shortcut method, `refresh()`, to handle this common pattern:

```php
$container->singleton(Auth::class, function (Container $container) {
    $auth = new Auth;
    $auth->setSession($container->make(Session::class));

    $container->refresh(Session::class, $auth, 'setSession');

    return $auth;
});
```

It also returns the existing instance or binding (if there is one), so you can do this:

```php
// This only works if you call singleton() or bind() on the class
$container->singleton(Session::class);

$container->singleton(Auth::class, function (Container $container) {
    $auth = new Auth;
    $auth->setSession($container->refresh(Session::class, $auth, 'setSession'));
    return $auth;
});
```

(Personally I find this syntax more confusing and prefer the more verbose version above!)

_**Note:** These methods are not part of the [Container interface](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Contracts/Container/Container.php), only the concrete [Container class](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Container/Container.php)._

## Overriding Constructor Parameters

The `makeWith()` method allows you to pass additional parameters to the constructor. It ignores any existing instances or singletons, and can be useful for creating multiple instances of a class with different parameters while still injecting dependencies:

```php
class Post
{
    public function __construct(Database $db, int $id) { /* ... */ }
}

```

```php
$post1 = $container->makeWith(Post::class, ['id' => 1]);
$post2 = $container->makeWith(Post::class, ['id' => 2]);
```

_**Note:** In Laravel 5.3 and below it was simply `make($class, $parameters)`. It was [removed in Laravel 5.4](https://github.com/laravel/internals/issues/391), but then [re-added as makeWith()](https://github.com/laravel/framework/pull/18271) in 5.4.16\. In Laravel 5.5 it looks like it will be [reverted back to the Laravel 5.3 syntax](https://github.com/laravel/framework/pull/19201)._

## Other Methods

That covers all of the methods I think are useful - but just to round things off, here's a summary of the remaining public methods...

### bound()

The `bound()` method returns true if the class or name has been bound with `bind()`, `singleton()`, `instance()` or `alias()`.

```php
if (! $container->bound('database.user')) {
    // ...
}
```

You can also use the array access syntax and `isset()`:

```php
if (! isset($container['database.user'])) {
    // ...
}
```

It can be reset with `unset()`, which removes the specified binding/instance/alias.

```php
unset($container['database.user']);
var_dump($container->bound('database.user')); // false
```

### bindIf()

`bindIf()` does the same thing as `bind()`, except it only registers a binding if one doesn't already exist (see `bound()` above). It could potentially be used to register a default binding in a package while allowing the user to override it.

```php
$container->bindIf(Loader::class, FallbackLoader::class);
```

There is no `singletonIf()` method, but you can use `bindIf($abstract, $concrete, true)` instead:

```php
$container->bindIf(Loader::class, FallbackLoader::class, true);
```

Or write it out in full:

```php
if (! $container->bound(Loader::class)) {
    $container->singleton(Loader::class, FallbackLoader::class);
}
```

### resolved()

The `resolved()` method returns true if a class has previously been resolved.

```php
var_dump($container->resolved(Database::class)); // false
$container->make(Database::class);
var_dump($container->resolved(Database::class)); // true
```

I'm not sure what it's useful for... It is reset if `unset()` is used (see `bound()` above).

```php
unset($container[Database::class]);
var_dump($container->resolved(Database::class)); // false
```

### factory()

The `factory()` method returns a closure that takes no parameters and calls `make()`.

```php
$dbFactory = $container->factory(Database::class);

$db = $dbFactory();
```

I'm not sure what it's useful for...

### wrap()

The `wrap()` method wraps a closure so that its dependencies will be injected when it is executed. The wrap method accepts an array of parameters; the returned closure takes no parameters:

```php
$cacheGetter = function (Cache $cache, $key) {
    return $cache->get($key);
};

$usernameGetter = $container->wrap($cacheGetter, ['username']);

$username = $usernameGetter();
```

I'm not sure what it's useful for, since the closure takes no parameters...

_**Note:** This method is not part of the [Container interface](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Contracts/Container/Container.php), only the concrete [Container class](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Container/Container.php)._

### afterResolving()

The `afterResolving()` method works exactly the same as `resolving()`, except the "afterResolving" callbacks are called after the "resolving" callbacks. I'm not sure when that would be useful...

### And Finally...

- `isShared()` - Determines if a given type is a shared singleton/instance
- `isAlias()` - Determines if a given string is a registered alias
- `hasMethodBinding()` - Determines if the container has a given method binding
- `getBindings()` - Retrieves the raw array of all registered bindings
- `getAlias($abstract)` - Resolves an alias to the underlying class/binding name
- `forgetInstance($abstract)` - Clears a single instance object
- `forgetInstances()` - Clears all instance objects
- `flush()` - Clear all bindings and instances, effectively resetting the container
- `setInstance()` - Replaces the instance used by `getInstance()` (Tip: Use `setInstance(null)` to clear it, so next time it will generate a new instance)

_**Note:** None of the methods in this last section are part of the [Container interface](https://github.com/laravel/framework/blob/5.4/src/Illuminate/Contracts/Container/Container.php)._

---

*This article was originally posted on DaveJamesMiller.com on 15 June 2017.*