<?php
// @see: https://www.php.net/releases/8.4/en.php
// @see: https://www.php.net/manual/en/language.oop5.lazy-objects.php
// @see: https://www.youtube.com/watch?v=7J6Z0F4vItw

// A simple Foo class with a constructor, property and method...
class Foo
{
    public string $foo;
    
    public function __construct()
    {
        echo __CLASS__ . ' constructed!';
    }
    
    public function bar(): string
    {
        return 'bar';
    }
}

// A helper to make a lazy proxy object using reflection...
function lazy(string $class, callable $init): object
{
    return new ReflectionClass($class)->newLazyProxy($init);
}

// A closure to initialise a lazy object...
$init = function () {
    $foo = new Foo;
    $foo->foo = 'foo';

    return $foo;
};

// Create a lazy proxy object instance of Foo...
$object = lazy(Foo::class, $init);

// It's definitely and instance of Foo, however, it's not been constructed yet...
var_dump(get_class($object));

// We can pass it to a typed function fine...
(function (Foo $foo) {
    $foo->foo; // Accessing the property initialises the object (the method does not)...
})($object);