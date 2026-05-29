<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Redis;

use Illuminate\Support\Facades\Redis;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class RedisConnectDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'redis.connect';
    }

    public function label(): string
    {
        return 'Redis Connection';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $connection = $options['connection'] ?? 'default';

        try {
            [$pong, $durationMs] = $this->benchmark(
                fn () => Redis::connection($connection)->ping()
            );

            if ($pong !== true && mb_strtolower((string) $pong) !== 'pong') {
                return DiagnosticResult::failed(
                    message: "Redis PING failed on [{$connection}]",
                    context: ['connection' => $connection],
                );
            }

            return DiagnosticResult::ok(
                message: "Redis PING succeeded on [{$connection}]",
                durationMs: $durationMs,
                context: ['connection' => $connection],
            );
        } catch (Throwable $e) {
            return DiagnosticResult::failed(
                message: "Redis connection failed on [{$connection}]: {$e->getMessage()}",
                context: ['connection' => $connection],
            );
        }
    }
}
