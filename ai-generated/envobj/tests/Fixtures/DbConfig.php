<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Fixtures;

use Ziadoz\EnvObj\ArrayableConfig;
use Ziadoz\EnvObj\Attributes\EnvFallback;
use Ziadoz\EnvObj\Attributes\EnvString;

readonly class DbConfig extends ArrayableConfig
{
    #[EnvString('DB_HOST')]
    public string $host;

    #[EnvString('DB_NAME')]
    public string $database;

    #[EnvFallback(
        new EnvString('DB_USERNAME'),
        new EnvString('DB_USER'),
    )]
    public string $username;

    #[EnvFallback(
        new EnvString('DB_PASSWORD'),
        new EnvString('DB_PASS'),
    )]
    public string $password;
}
