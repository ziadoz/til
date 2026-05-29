<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Env;

use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class EnvKeyDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'env.key';
    }

    public function label(): string
    {
        return 'Application Key';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $key = config('app.key');

        if (empty($key)) {
            return DiagnosticResult::failed('APP_KEY is not set');
        }

        return DiagnosticResult::ok('APP_KEY is set');
    }
}
