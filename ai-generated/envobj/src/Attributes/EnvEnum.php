<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

use Attribute;
use BackedEnum;
use ValueError;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvEnum implements EnvVar
{
    public function __construct(
        public string $key,
        public string $enumClass,
        public bool $nullable = false,
    ) {
    }

    public function read(): ?BackedEnum
    {
        $raw = EnvReader::get()->get($this->key);

        if ($raw === null || $raw === '') {
            if ($this->nullable) {
                return null;
            }

            throw InvalidEnvException::missingRequired($this->key);
        }

        if ($this->nullable) {
            return ($this->enumClass)::tryFrom($raw);
        }

        try {
            return ($this->enumClass)::from($raw);
        } catch (ValueError) {
            throw InvalidEnvException::invalidEnum($this->key, $this->enumClass, $raw);
        }
    }
}
