<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Exceptions;

use RuntimeException;

class InvalidEnvException extends RuntimeException
{
    public static function missingRequired(string $key): self
    {
        return new self("Missing required env var: {$key}");
    }

    public static function typeMismatch(string $key, string $expectedType, string $value): self
    {
        return new self("Env var '{$key}' cannot be cast to {$expectedType}: '{$value}'");
    }

    public static function invalidEnum(string $key, string $enumClass, string $value): self
    {
        return new self("Env var '{$key}' value '{$value}' is not valid for enum {$enumClass}");
    }
}
