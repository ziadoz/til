<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Fixtures;

enum AppEnvEnum: string
{
    case Production  = 'production';
    case Development = 'development';
    case Testing     = 'testing';
}
