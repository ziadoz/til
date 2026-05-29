<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Fixtures;

use Ziadoz\EnvObj\ArrayableConfig;

readonly class AppConfig extends ArrayableConfig
{
    public DbConfig $db;
    public DbOptionsConfig $dbOptions;

    public function __construct(public DbModesConfig $modes = new DbModesConfig())
    {
    }
}
