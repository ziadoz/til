<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Attributes;

interface EnvVar
{
    public function read(): mixed;
}
