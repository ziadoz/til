<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

use Attribute;
use Closure;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;

/**
 * Note: Closures cannot be used in PHP attribute arguments.
 * Use EnvCallable via direct instantiation, not as a declarative attribute.
 *
 * Example:
 *   $attr = new EnvCallable('MY_VAR', fn(string $raw) => strtoupper($raw));
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvCallable implements EnvVar
{
    public function __construct(
        public string $key,
        public Closure $callable,
        public bool $nullable = false,
    ) {
    }

    public function read(): mixed
    {
        $raw = EnvReader::get()->get($this->key);

        if ($raw === null || $raw === '') {
            if ($this->nullable) {
                return null;
            }

            throw InvalidEnvException::missingRequired($this->key);
        }

        return ($this->callable)($raw);
    }
}
