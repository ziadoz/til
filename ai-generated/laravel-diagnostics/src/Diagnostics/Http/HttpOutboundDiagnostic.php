<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Http;

use Illuminate\Support\Facades\Http;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class HttpOutboundDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'http.outbound';
    }

    public function label(): string
    {
        return 'HTTP Outbound';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $url = $options['url'] ?? config('diagnostics.http.url');

        try {
            [$response, $durationMs] = $this->benchmark(
                fn () => Http::timeout(10)->get($url)
            );

            if ($response->successful() || $response->redirect()) {
                return DiagnosticResult::ok(
                    message: "Reached [{$url}] with status [{$response->status()}]",
                    durationMs: $durationMs,
                    context: ['url' => $url, 'status' => $response->status()],
                );
            }

            return DiagnosticResult::failed(
                message: "Reached [{$url}] but got unexpected status [{$response->status()}]",
                durationMs: $durationMs,
                context: ['url' => $url, 'status' => $response->status()],
            );
        } catch (Throwable $e) {
            return DiagnosticResult::failed(
                message: "Failed to reach [{$url}]: {$e->getMessage()}",
                context: ['url' => $url],
            );
        }
    }
}
