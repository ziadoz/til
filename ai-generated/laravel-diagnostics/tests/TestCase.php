<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Ziadoz\LaravelDiagnostics\LaravelDiagnosticsServiceProvider;

final class TestCase extends Orchestra
{
    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelDiagnosticsServiceProvider::class,
        ];
    }
}
