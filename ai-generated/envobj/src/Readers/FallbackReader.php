<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Readers;

class FallbackReader implements ReaderInterface
{
    /** @var ReaderInterface[] */
    private array $readers;

    public function __construct(ReaderInterface ...$readers)
    {
        $this->readers = $readers;
    }

    public function get(string $key): ?string
    {
        foreach ($this->readers as $reader) {
            $value = $reader->get($key);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }
}
