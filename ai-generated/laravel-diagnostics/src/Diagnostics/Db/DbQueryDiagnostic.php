<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Db;

use Illuminate\Support\Facades\DB;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class DbQueryDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'db.query';
    }

    public function label(): string
    {
        return 'Database Query';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $connection = $options['connection'] ?? config('database.default');

        try {
            [, $durationMs] = $this->benchmark(
                fn () => DB::connection($connection)->selectOne('SELECT 1')
            );

            return DiagnosticResult::ok(
                message: "Query executed on [{$connection}]",
                durationMs: $durationMs,
                context: ['connection' => $connection],
            );
        } catch (Throwable $e) {
            return DiagnosticResult::failed(
                message: "Failed to query [{$connection}]: {$e->getMessage()}",
                context: ['connection' => $connection],
            );
        }
    }
}
