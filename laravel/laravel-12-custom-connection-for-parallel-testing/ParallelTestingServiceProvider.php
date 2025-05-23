<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Concerns\TestDatabases;
use Illuminate\Testing\ParallelTestingServiceProvider as LaravelParallelTestingServiceProvider;

// Laravel doesn't support changing the database connection when running tests in parallel, so we have to override the relevant method.
// @see: https://sarahjting.com/blog/laravel-paratest-multiple-db-connections
class ParallelTestingServiceProvider extends LaravelParallelTestingServiceProvider
{
    use TestDatabases {
        switchToDatabase as protected switchToDatabaseDefault;
    }

    protected string $connectionToParaTest = 'my-connection'; // Your custom connection.

    protected function ensureSchemaIsUpToDate(): void
    {
        if (! self::$schemaIsUpToDate) {
            Artisan::call('migrate', ['database' => $this->connectionToParaTest]);

            self::$schemaIsUpToDate = true;
        }
    }

    protected function switchToDatabase($database): void
    {
        config()->set('database.default', $this->connectionToParaTest);

        $this->switchToDatabaseDefault($database);
    }
}