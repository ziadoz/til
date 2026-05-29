<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Db;

use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class DbConnectDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'db.connect';
    }

    public function label(): string
    {
        return 'Database Connection';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $connection = $options['connection'] ?? config('database.default');

        try {
            [$pdo, $durationMs] = $this->benchmark(
                fn () => DB::connection($connection)->getPdo()
            );

            return DiagnosticResult::ok(
                message: "Connected to [{$connection}]",
                durationMs: $durationMs,
                context: [
                    'connection' => $connection,
                    'driver' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
                    'server_version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
                ],
            );
        } catch (Throwable $e) {
            return DiagnosticResult::failed(
                message: "Failed to connect to [{$connection}]: {$e->getMessage()}",
                context: ['connection' => $connection],
            );
        }
    }
}
