<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Env;

use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class EnvDebugDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'env.debug';
    }

    public function label(): string
    {
        return 'Debug Mode Off';
    }

    public function environments(): array
    {
        return ['production'];
    }

    public function run(array $options = []): DiagnosticResult
    {
        if (config('app.debug') === true) {
            return DiagnosticResult::failed('APP_DEBUG is enabled in production');
        }

        return DiagnosticResult::ok('APP_DEBUG is disabled');
    }
}
