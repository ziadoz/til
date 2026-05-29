<?php

declare(strict_types=1);

use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticStatus;

it('creates an ok result', function (): void {
    $result = DiagnosticResult::ok('All good', rawOutput: 'raw', durationMs: 12.5, context: ['key' => 'val']);

    expect($result->status)->toBe(DiagnosticStatus::Ok);
    expect($result->message)->toBe('All good');
    expect($result->rawOutput)->toBe('raw');
    expect($result->durationMs)->toBe(12.5);
    expect($result->context)->toBe(['key' => 'val']);
});

it('creates a failed result', function (): void {
    $result = DiagnosticResult::failed('Something broke');

    expect($result->status)->toBe(DiagnosticStatus::Failed);
    expect($result->message)->toBe('Something broke');
    expect($result->rawOutput)->toBeNull();
    expect($result->durationMs)->toBeNull();
    expect($result->context)->toBeEmpty();
});

it('creates a skipped result', function (): void {
    $result = DiagnosticResult::skipped('Not in this env');

    expect($result->status)->toBe(DiagnosticStatus::Skipped);
    expect($result->message)->toBe('Not in this env');
});

it('enriches a result with diagnostic name and label', function (): void {
    $result = DiagnosticResult::ok('All good')->forDiagnostic('db.connect', 'Database Connection');

    expect($result->name)->toBe('db.connect');
    expect($result->label)->toBe('Database Connection');
    expect($result->status)->toBe(DiagnosticStatus::Ok);
});

it('name and label default to empty string', function (): void {
    $result = DiagnosticResult::ok('All good');

    expect($result->name)->toBe('');
    expect($result->label)->toBe('');
});
