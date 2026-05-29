<?php

declare(strict_types=1);

use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRunner;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticStatus;

final class OkDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'ok.diagnostic';
    }

    public function label(): string
    {
        return 'OK Diagnostic';
    }

    public function run(array $options = []): DiagnosticResult
    {
        return DiagnosticResult::ok('All good');
    }
}

final class ProductionOnlyDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'production.only';
    }

    public function label(): string
    {
        return 'Production Only';
    }

    public function environments(): array
    {
        return ['production'];
    }

    public function run(array $options = []): DiagnosticResult
    {
        return DiagnosticResult::ok('Running in production');
    }
}

final class OptionsCaptureDiagnostic extends Diagnostic
{
    public array $capturedOptions = [];

    public function name(): string
    {
        return 'options.capture';
    }

    public function label(): string
    {
        return 'Options Capture';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $this->capturedOptions = $options;

        return DiagnosticResult::ok('Captured');
    }
}

it('runs a diagnostic and returns an enriched result', function (): void {
    $runner = new DiagnosticRunner;
    $result = $runner->run(new OkDiagnostic);

    expect($result)->toBeInstanceOf(DiagnosticResult::class);
    expect($result->status)->toBe(DiagnosticStatus::Ok);
    expect($result->name)->toBe('ok.diagnostic');
    expect($result->label)->toBe('OK Diagnostic');
});

it('skips diagnostics not matching the current environment', function (): void {
    $runner = new DiagnosticRunner;
    $result = $runner->run(new ProductionOnlyDiagnostic);

    expect($result->status)->toBe(DiagnosticStatus::Skipped);
    expect($result->message)->toContain('testing');
});

it('passes options to the diagnostic', function (): void {
    $diagnostic = new OptionsCaptureDiagnostic;
    $runner = new DiagnosticRunner;
    $runner->run($diagnostic, ['disk' => 's3', 'connection' => 'mysql']);

    expect($diagnostic->capturedOptions)->toBe(['disk' => 's3', 'connection' => 'mysql']);
});

it('runs many diagnostics and returns enriched results', function (): void {
    $runner = new DiagnosticRunner;
    $results = $runner->runMany([new OkDiagnostic, new ProductionOnlyDiagnostic]);

    expect($results)->toHaveCount(2);
    expect($results->first()->name)->toBe('ok.diagnostic');
});
