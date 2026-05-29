<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

use Attribute;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvBool implements EnvVar
{
    private const array TRUTHY = ['true', '1', 'yes', 'on'];
    private const array FALSY  = ['false', '0', 'no', 'off', ''];

    public function __construct(
        public string $key,
        public ?bool $default = null,
        public bool $nullable = false,
    ) {
    }

    public function read(): ?bool
    {
        $raw = EnvReader::get()->get($this->key);

        if ($raw === null) {
            if ($this->nullable) {
                return null;
            }

            if ($this->default !== null) {
                return $this->default;
            }

            throw InvalidEnvException::missingRequired($this->key);
        }

        $lower = strtolower($raw);

        if (in_array($lower, self::TRUTHY, true)) {
            return true;
        }

        if (in_array($lower, self::FALSY, true)) {
            return false;
        }

        throw InvalidEnvException::typeMismatch($this->key, 'bool', $raw);
    }
}
