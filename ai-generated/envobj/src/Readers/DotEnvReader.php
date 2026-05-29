<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Readers;

use RuntimeException;

class DotEnvReader implements ReaderInterface
{
    private array $values = [];

    public function __construct(string $path, string $filename = '.env')
    {
        if (! class_exists(\Dotenv\Dotenv::class)) {
            throw new RuntimeException('vlucas/phpdotenv is required to use DotEnvReader. Run: composer require vlucas/phpdotenv');
        }

        $dotenv = \Dotenv\Dotenv::createImmutable($path, $filename, false);
        $this->values = $dotenv->load();
    }

    public function get(string $key): ?string
    {
        $value = $this->values[$key] ?? null;

        return $value !== null ? (string) $value : null;
    }
}
