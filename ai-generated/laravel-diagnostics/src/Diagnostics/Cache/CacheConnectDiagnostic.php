<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Cache;

use Illuminate\Support\Facades\Cache;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class CacheConnectDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'cache.connect';
    }

    public function label(): string
    {
        return 'Cache Connection';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $store = $options['store'] ?? config('cache.default');

        try {
            [, $durationMs] = $this->benchmark(
                fn () => Cache::store($store)->has('__diagnostics_probe__')
            );

            return DiagnosticResult::ok(
                message: "Connected to cache store [{$store}]",
                durationMs: $durationMs,
                context: [
                    'store' => $store,
                    'driver' => config("cache.stores.{$store}.driver"),
                ],
            );
        } catch (Throwable $e) {
            return DiagnosticResult::failed(
                message: "Failed to connect to cache store [{$store}]: {$e->getMessage()}",
                context: ['store' => $store],
            );
        }
    }
}
