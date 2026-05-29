<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

use Attribute;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvArray implements EnvVar
{
    public function __construct(
        public string $key,
        public ?array $default = null,
        public bool $nullable = false,
        public string $separator = ',',
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

        return array_map('trim', explode($this->separator, $raw));
    }
}
