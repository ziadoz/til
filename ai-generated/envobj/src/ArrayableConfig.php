<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj;

use ArrayAccess;
use JsonSerializable;
use RuntimeException;

readonly class ArrayableConfig extends Config implements ArrayAccess, JsonSerializable
{
    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (str_contains((string) $offset, '.')) {
            $keys   = explode('.', $offset);
            $result = $this;

            foreach ($keys as $key) {
                if (! ($result instanceof ArrayAccess) || ! $result->offsetExists($key)) {
                    throw new RuntimeException("Missing config property: {$offset}");
                }

                $result = $result->offsetGet($key);
            }

            return $result;
        }

        if (! $this->offsetExists($offset)) {
            throw new RuntimeException("Missing config property: {$offset}");
        }

        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Cannot set config property: ' . $offset);
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Cannot unset config property: ' . $offset);
    }

    public function toArray(): array
    {
        $array = [];

        foreach (get_object_vars($this) as $field => $value) {
            if ($value instanceof ArrayableConfig) {
                $array[$field] = $value->toArray();
            } else {
                $array[$field] = $value;
            }
        }

        return $array;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
