<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvFallback implements EnvVar
{
    /** @var EnvVar[] */
    public array $envVars;

    public function __construct(EnvVar ...$envVars)
    {
        $this->envVars = $envVars;
    }

    public function read(): mixed
    {
        $last = null;

        foreach ($this->envVars as $envVar) {
            $last = $envVar;

            try {
                $value = $envVar->read();

                if ($value !== null) {
                    return $value;
                }
            } catch (\Throwable) {
                // Try next attribute
            }
        }

        // If all failed, try the last one without catching (will throw if required)
        return $last?->read();
    }
}
