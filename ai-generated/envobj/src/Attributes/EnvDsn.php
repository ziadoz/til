<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

use Attribute;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvDsn implements EnvVar
{
    public function __construct(
        public string $key,
        public ?array $default = null,
        public bool $nullable = false,
    ) {
    }

    public function read(): ?array
    {
        $raw = EnvReader::get()->get($this->key);

        if ($raw === null || $raw === '') {
            if ($this->nullable) {
                return null;
            }

            if ($this->default !== null) {
                return $this->default;
            }

            throw InvalidEnvException::missingRequired($this->key);
        }

        $parsed = parse_url($raw);

        if ($parsed === false) {
            throw InvalidEnvException::typeMismatch($this->key, 'DSN', $raw);
        }

        return $parsed;
    }
}
