<?php

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;
use Illuminate\Support\Facades\Artisan;

#[Attribute(Attribute::TARGET_PARAMETER)]
class LazyGhost implements ContextualAttribute
{
    /**
     * Provide a concrete class implementation for dependency injection.
     *
     * @template T
     *
     * @param  class-string<T>  $class
     * @param  array  $params
     */
    public function __construct(
        public string $class,
        public array $params = [],
    ) {
    }

    /**
     * Resolve the dependency.
     */
    public static function resolve(self $attribute, Container $container)
    {
        $class = $container->getAlias($attribute->class) ?? $attribute->class;

        return (new ReflectionClass($class))->newLazyGhost(function ($object) use ($attribute, $container) {
            if (method_exists($object, '__construct')) {
                $container->call($object->__construct(...), $attribute->params);
            }
        });
    }
}

/**
 * Dependencies
 */
class Foo
{
    public string $foo = 'foo';

    public function __construct(public string $a, public string $b)
    {
    }
}

class Bar
{
    public string $bar = 'bar';

    // No constructor, just to be thorough...
}

class Baz
{
    public function __construct(
        #[LazyGhost('alias', ['a' => 'aaa', 'b' => 'bbb'])] public Foo $foo,
        #[LazyGhost(Bar::class)] public Bar $bar,
    ) {
    }
}

/**
 * Dependant
 */
class Qux
{
    public function __construct(
        #[LazyGhost(Foo::class, ['a' => 'a', 'b' => 'b'])] public Foo $foo,
        #[LazyGhost(Bar::class)] public Bar $bar,
        #[LazyGhost(Baz::class)] public Baz $baz,
    ) {
    }
}

Artisan::command('scratch', function () {
    /**
     * Binds
     */
    // Foo has an alias...
    app()->bind(Foo::class);
    app()->alias(Foo::class, 'alias');

    // Bar doesn't have an alias...
    app()->bind(Bar::class);

    // No bind for Baz...
    // This doesn't work for singleton bindings...

    /**
     * Usage
     */
    $qux = app()->make(Qux::class);
    dump((new ReflectionClass($qux->foo))->isUninitializedLazyObject($qux->foo));
    dump((new ReflectionClass($qux->bar))->isUninitializedLazyObject($qux->bar));
    dump((new ReflectionClass($qux->baz))->isUninitializedLazyObject($qux->baz));

    // Access the lazy ghost properties to initialise them...
    $qux->foo->foo;
    $qux->bar->bar;
    $qux->baz->foo->foo;
    $qux->baz->bar->bar;
    dump((new ReflectionClass($qux->foo))->isUninitializedLazyObject($qux->foo));
    dump((new ReflectionClass($qux->bar))->isUninitializedLazyObject($qux->bar));
    dump((new ReflectionClass($qux->baz))->isUninitializedLazyObject($qux->baz));

    dump($qux->foo, $qux->bar, $qux->baz);
});

/*
❯ php artisan scratch
true // routes/console.php:99
true // routes/console.php:100
true // routes/console.php:101
false // routes/console.php:107
false // routes/console.php:108
false // routes/console.php:109
Foo^ {#1191
  +foo: "foo"
  +a: "a"
  +b: "b"
} // routes/console.php:111
Bar^ {#1194
  +bar: "bar"
} // routes/console.php:111
Baz^ {#1197
  +foo: Foo^ {#1185
    +foo: "foo"
    +a: "aaa"
    +b: "bbb"
  }
  +bar: Bar^ {#1199
    +bar: "bar"
  }
} // routes/console.php:111
*/