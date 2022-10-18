<?php
namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase as LaravelRefreshDatabase;

// Use a specific database connection for test migrations.
// @see: https://github.com/laravel/framework/pull/44630
// @see: https://github.com/laravel/framework/issues/36294
trait RefreshDatabase
{
    use LaravelRefreshDatabase {
        migrateFreshUsing as protected migrateFreshUsingDefaults;
    }

    protected function migrateFreshUsing()
    {
        $database = $this->connectionToMigrate();

        return array_merge(
            $this->migrateFreshUsingDefaults(),
            $database ? ['--database' => $database] : [],
        );
    }

    protected function connectionToMigrate(): string|bool
    {
        return property_exists($this, 'connectionToMigrate') ? $this->connectionToMigrate : false;
    }
}