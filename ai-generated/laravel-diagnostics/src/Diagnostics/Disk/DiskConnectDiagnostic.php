<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Disk;

use Illuminate\Support\Facades\Storage;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class DiskConnectDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'disk.connect';
    }

    public function label(): string
    {
        return 'Disk Connection';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $disk = $options['disk'] ?? config('filesystems.default');

        try {
            [, $durationMs] = $this->benchmark(
                fn () => Storage::disk($disk)->exists('__diagnostics_probe__')
            );

            return DiagnosticResult::ok(
                message: "Connected to disk [{$disk}]",
                durationMs: $durationMs,
                context: [
                    'disk' => $disk,
                    'driver' => config("filesystems.disks.{$disk}.driver"),
                ],
            );
        } catch (Throwable $e) {
            return DiagnosticResult::failed(
                message: "Failed to connect to disk [{$disk}]: {$e->getMessage()}",
                context: ['disk' => $disk],
            );
        }
    }
}
