<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Fixtures;

use Ziadoz\EnvObj\ArrayableConfig;
use Ziadoz\EnvObj\Attributes\EnvArray;

readonly class DbModesConfig extends ArrayableConfig
{
    #[EnvArray('DB_MODES')]
    public array $modes;
}
