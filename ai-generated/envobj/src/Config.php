<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj;

use ReflectionAttribute;
use ReflectionClass;
use RuntimeException;
use Ziadoz\EnvObj\Attributes\EnvVar;

readonly class Config
{
    /**
     * Cloning is the mechanism for populating readonly properties.
     * @see https://stitcher.io/blog/cloning-readonly-properties-in-php-83
     */
    final public function __clone(): void
    {
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                throw new RuntimeException('Cannot populate static property: ' . $property->getName());
            }

            if (! $property->isReadOnly()) {
                throw new RuntimeException('Cannot populate non-readonly property: ' . $property->getName());
            }

            $field = $property->getName();
            $type  = $property->getType()?->getName();

            // Nested Config subclass declared as a type
            if ($type !== null && is_a($type, Config::class, true)) {
                $this->$field = new $type()->load();
                continue;
            }

            // Nested Config subclass already initialised as a property value
            if ($property->isInitialized($this)) {
                $value = $property->getValue($this);

                if ($value instanceof Config) {
                    $this->$field = (new ($value::class)())->load();
                    continue;
                }
            }

            // EnvVar attributes
            $attributes = $property->getAttributes(EnvVar::class, ReflectionAttribute::IS_INSTANCEOF);

            if (count($attributes) > 0) {
                foreach ($attributes as $attribute) {
                    $this->$field = $attribute->newInstance()->read();
                }
            }
        }
    }

    final public function load(): static
    {
        return clone $this;
    }
}
