<?php

declare(strict_types=1);

use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRegistry;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class AlwaysOkDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'always.ok';
    }

    public function label(): string
    {
        return 'Always OK';
    }

    public function run(array $options = []): DiagnosticResult
    {
        return DiagnosticResult::ok('Passed');
    }
}

final class AlwaysFailedDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'always.failed';
    }

    public function label(): string
    {
        return 'Always Failed';
    }

    public function run(array $options = []): DiagnosticResult
    {
        return DiagnosticResult::failed('Broken');
    }
}

beforeEach(function (): void {
    $registry = $this->app->make(DiagnosticRegistry::class);

    foreach ($registry->all() as $d) {
        $registry->forget($d->name());
    }

    $registry->register(AlwaysOkDiagnostic::class);
    $registry->register(AlwaysFailedDiagnostic::class);
});

it('returns success when all diagnostics pass', function (): void {
    $registry = $this->app->make(DiagnosticRegistry::class);
    $registry->forget('always.failed');

    $this->artisan('diagnostics:run')
        ->assertSuccessful();
});

it('returns failure when any diagnostic fails', function (): void {
    $this->artisan('diagnostics:run')
        ->assertFailed();
});

it('runs only named diagnostics', function (): void {
    $this->artisan('diagnostics:run', ['names' => ['always.ok']])
        ->assertSuccessful()
        ->expectsOutputToContain('always.ok');
});

it('outputs json format', function (): void {
    $this->artisan('diagnostics:run', ['names' => ['always.ok'], '--format' => 'json'])
        ->assertSuccessful();
});

it('outputs verbose format', function (): void {
    $this->artisan('diagnostics:run', ['names' => ['always.ok'], '--format' => 'verbose'])
        ->assertSuccessful()
        ->expectsOutputToContain('Always OK');
});

it('returns failure when no diagnostics are found', function (): void {
    $this->artisan('diagnostics:run', ['names' => ['does.not.exist']])
        ->assertFailed();
});
