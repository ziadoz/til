<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

use Attribute;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvArrayMap implements EnvVar
{
    public function __construct(
        public string $key,
        public ?array $default = null,
        public bool $nullable = false,
        public string $pairSeparator = ',',
        public string $kvSeparator = '=',
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

        $result = [];

        foreach (explode($this->pairSeparator, $raw) as $pair) {
            $parts = explode($this->kvSeparator, trim($pair), 2);

            if (count($parts) !== 2) {
                throw InvalidEnvException::typeMismatch($this->key, 'array map', $pair);
            }

            $result[trim($parts[0])] = trim($parts[1]);
        }

        return $result;
    }
}
