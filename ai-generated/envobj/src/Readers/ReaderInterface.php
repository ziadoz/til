<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Readers;

interface ReaderInterface
{
    public function get(string $key): ?string;
}
