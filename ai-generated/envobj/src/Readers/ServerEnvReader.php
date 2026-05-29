<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Readers;

class ServerEnvReader implements ReaderInterface
{
    public function get(string $key): ?string
    {
        $value = $_ENV[$key] ?? null;

        return $value !== null ? (string) $value : null;
    }
}
