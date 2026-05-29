<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Disk;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class DiskWriteDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'disk.write';
    }

    public function label(): string
    {
        return 'Disk Write / Read / Delete';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $disk = $options['disk'] ?? config('filesystems.default');
        $path = 'laravel-diagnostics/'.Str::random(8).'.txt';
        $content = Str::random(32);

        try {
            [$written, $writeDurationMs] = $this->benchmark(
                fn () => Storage::disk($disk)->put($path, $content)
            );

            if (! $written) {
                return DiagnosticResult::failed(
                    message: "Disk write failed on [{$disk}]",
                    context: ['disk' => $disk],
                );
            }

            [$read, $readDurationMs] = $this->benchmark(
                fn () => Storage::disk($disk)->get($path)
            );

            if ($read !== $content) {
                Storage::disk($disk)->delete($path);

                return DiagnosticResult::failed(
                    message: "Disk read returned unexpected content on [{$disk}]",
                    context: ['disk' => $disk],
                );
            }

            Storage::disk($disk)->delete($path);

            return DiagnosticResult::ok(
                message: "Disk write/read/delete succeeded on [{$disk}]",
                durationMs: $writeDurationMs + $readDurationMs,
                context: [
                    'disk' => $disk,
                    'driver' => config("filesystems.disks.{$disk}.driver"),
                ],
            );
        } catch (Throwable $e) {
            Storage::disk($disk)->delete($path);

            return DiagnosticResult::failed(
                message: "Disk check failed on [{$disk}]: {$e->getMessage()}",
                context: ['disk' => $disk],
            );
        }
    }
}
