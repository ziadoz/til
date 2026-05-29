<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Readers;

class GetEnvReader implements ReaderInterface
{
    public function get(string $key): ?string
    {
        $value = getenv($key);

        return $value !== false ? $value : null;
    }
}
