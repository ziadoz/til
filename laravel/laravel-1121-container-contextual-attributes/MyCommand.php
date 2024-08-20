<?php
/*
Contextual attributes were implementated by @innocenzi and @ollieread:

- https://github.com/laravel/framework/pull/51934
- https://github.com/laravel/framework/pull/52428

I added contextual attributes for the core drivers:

- https://github.com/laravel/framework/pull/52265

You can use them in your application like this:
*/

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Attributes\Cache;
use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\Database;
use Illuminate\Container\Attributes\Log;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Connection;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class MyCommand extends Command
{
    protected $signature = 'app:my-command';

    protected $description = 'Command description';

    public function handle(
        // Storage
        #[Storage('local')] Filesystem $fsLocal,
        #[Storage('public')] Filesystem $fsPublic,

        // Logger
        #[Log('null')] LoggerInterface $logNull,
        #[Log('stderr')] LoggerInterface $logStderr,

        // Cache
        #[Cache('array')] CacheInterface $cacheArray,
        #[Cache('file')] CacheInterface $cacheFile,

        // Database
        #[Database('sqlite')] Connection $dbSqlite,
        #[Database('mysql')] Connection $dbMysql,

        // Config
        #[Config('app.name')] string $configAppName,
    ): void {
        // Storage
        dump($fsLocal, $fsPublic);

        // Logger
        dump($logNull, $logStderr);

        // Cache
        dump($cacheArray, $cacheFile);

        // Database
        dump($dbSqlite, $dbMysql);

        // Config
        dump($configAppName);
    }
}
