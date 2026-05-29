<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Queue;

use Illuminate\Support\Facades\Queue;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class QueueConnectDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'queue.connect';
    }

    public function label(): string
    {
        return 'Queue Connection';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $connection = $options['connection'] ?? config('queue.default');

        try {
            [, $durationMs] = $this->benchmark(
                fn () => Queue::connection($connection)
            );

            return DiagnosticResult::ok(
                message: "Connected to queue [{$connection}]",
                durationMs: $durationMs,
                context: [
                    'connection' => $connection,
                    'driver' => config("queue.connections.{$connection}.driver"),
                ],
            );
        } catch (Throwable $e) {
            return DiagnosticResult::failed(
                message: "Failed to connect to queue [{$connection}]: {$e->getMessage()}",
                context: ['connection' => $connection],
            );
        }
    }
}
