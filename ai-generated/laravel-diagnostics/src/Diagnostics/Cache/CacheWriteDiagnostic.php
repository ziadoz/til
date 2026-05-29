<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class CacheWriteDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'cache.write';
    }

    public function label(): string
    {
        return 'Cache Write / Read / Delete';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $store = $options['store'] ?? config('cache.default');
        $key = 'laravel-diagnostics:'.Str::random(8);
        $value = Str::random(16);

        try {
            [$written, $writeDurationMs] = $this->benchmark(
                fn () => Cache::store($store)->put($key, $value, 10)
            );

            if (! $written) {
                return DiagnosticResult::failed(
                    message: "Cache write failed on [{$store}]",
                    context: ['store' => $store],
                );
            }

            [$read, $readDurationMs] = $this->benchmark(
                fn () => Cache::store($store)->get($key)
            );

            if ($read !== $value) {
                Cache::store($store)->forget($key);

                return DiagnosticResult::failed(
                    message: "Cache read returned unexpected value on [{$store}]",
                    context: ['store' => $store],
                );
            }

            Cache::store($store)->forget($key);

            return DiagnosticResult::ok(
                message: "Cache write/read/delete succeeded on [{$store}]",
                durationMs: $writeDurationMs + $readDurationMs,
                context: [
                    'store' => $store,
                    'driver' => config("cache.stores.{$store}.driver"),
                ],
            );
        } catch (Throwable $e) {
            Cache::store($store)->forget($key);

            return DiagnosticResult::failed(
                message: "Cache check failed on [{$store}]: {$e->getMessage()}",
                context: ['store' => $store],
            );
        }
    }
}
