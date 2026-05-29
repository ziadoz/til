<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Readers;

readonly class PrefixedReader implements ReaderInterface
{
    public function __construct(
        private string $prefix,
        private ReaderInterface $reader,
    ) {
    }

    public function get(string $key): ?string
    {
        return $this->reader->get($this->prefix . $key);
    }
}
