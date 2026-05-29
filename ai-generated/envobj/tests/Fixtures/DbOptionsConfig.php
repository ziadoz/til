<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Fixtures;

use Ziadoz\EnvObj\ArrayableConfig;
use Ziadoz\EnvObj\Attributes\EnvInteger;

readonly class DbOptionsConfig extends ArrayableConfig
{
    #[EnvInteger('DB_OPTIONS_SSL_CA')]
    public int $sslCa;
}
