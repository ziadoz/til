<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ziadoz\LaravelDiagnostics\Commands\ListDiagnosticsCommand;
use Ziadoz\LaravelDiagnostics\Commands\RunDiagnosticsCommand;
use Ziadoz\LaravelDiagnostics\Diagnostics\Cache\CacheConnectDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Cache\CacheWriteDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Db\DbConnectDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Db\DbQueryDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRegistry;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRunner;
use Ziadoz\LaravelDiagnostics\Diagnostics\Disk\DiskConnectDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Disk\DiskWriteDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Env\EnvDebugDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Env\EnvKeyDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Http\HttpOutboundDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Mail\MailConnectDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Queue\QueueConnectDiagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\Redis\RedisConnectDiagnostic;

final class LaravelDiagnosticsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-diagnostics')
            ->hasConfigFile()
            ->hasCommands([
                RunDiagnosticsCommand::class,
                ListDiagnosticsCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(DiagnosticRegistry::class);
        $this->app->singleton(DiagnosticRunner::class);
    }

    public function packageBooted(): void
    {
        $registry = $this->app->make(DiagnosticRegistry::class);

        $registry->register(DbConnectDiagnostic::class);
        $registry->register(DbQueryDiagnostic::class);
        $registry->register(CacheConnectDiagnostic::class);
        $registry->register(CacheWriteDiagnostic::class);
        $registry->register(QueueConnectDiagnostic::class);
        $registry->register(DiskConnectDiagnostic::class);
        $registry->register(DiskWriteDiagnostic::class);
        $registry->register(MailConnectDiagnostic::class);
        $registry->register(HttpOutboundDiagnostic::class);
        $registry->register(RedisConnectDiagnostic::class);
        $registry->register(EnvKeyDiagnostic::class);
        $registry->register(EnvDebugDiagnostic::class);

        foreach (config('diagnostics.diagnostics', []) as $diagnostic) {
            $registry->register($diagnostic);
        }
    }
}
