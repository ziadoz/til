<?php

namespace Illuminate\Container\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

// The equivalent of doing `$request->query($key, $default)` in a controller.

#[Attribute(Attribute::TARGET_PARAMETER)]
class Query implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ?string $key = null, public mixed $default = null)
    {
    }

    /**
     * Resolve the GET data from the request.
     */
    public static function resolve(self $attribute, Container $container): mixed
    {
        return $container->make('request')->query($attribute->key, $attribute->default);
    }
}
