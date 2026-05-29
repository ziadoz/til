<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvPrefix implements EnvVar
{
    public function __construct(
        public string $prefix,
        public EnvVar $envVar,
    ) {
    }

    public function read(): mixed
    {
        // Delegate to a clone of the wrapped attribute with the prefixed key
        $class = $this->envVar::class;
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        // Build args: replace the first param (key) with prefix + key
        $args = [];
        foreach ($constructor->getParameters() as $i => $param) {
            if ($i === 0) {
                $args[] = $this->prefix . $this->envVar->key;
            } else {
                $name = $param->getName();
                if ($reflection->hasProperty($name) && $reflection->getProperty($name)->isInitialized($this->envVar)) {
                    $args[] = $reflection->getProperty($name)->getValue($this->envVar);
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                }
            }
        }

        return $reflection->newInstanceArgs($args)->read();
    }
}
