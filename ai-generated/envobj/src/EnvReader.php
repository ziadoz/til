<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj;

use Ziadoz\EnvObj\Readers\ReaderInterface;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvReader
{
    private static ?ReaderInterface $reader = null;

    public static function set(ReaderInterface $reader): void
    {
        self::$reader = $reader;
    }

    public static function get(): ReaderInterface
    {
        return self::$reader ??= new ServerEnvReader();
    }

    public static function reset(): void
    {
        self::$reader = null;
    }
}
