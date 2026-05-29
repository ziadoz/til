<?php

declare(strict_types=1);

use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticStatus;

it('has correct values', function (): void {
    expect(DiagnosticStatus::Ok->value)->toBe('ok');
    expect(DiagnosticStatus::Failed->value)->toBe('failed');
    expect(DiagnosticStatus::Skipped->value)->toBe('skipped');
});

it('has correct labels', function (): void {
    expect(DiagnosticStatus::Ok->label())->toBe('OK');
    expect(DiagnosticStatus::Failed->label())->toBe('FAILED');
    expect(DiagnosticStatus::Skipped->label())->toBe('SKIPPED');
});

it('has correct colors', function (): void {
    expect(DiagnosticStatus::Ok->color())->toBe('green');
    expect(DiagnosticStatus::Failed->color())->toBe('red');
    expect(DiagnosticStatus::Skipped->color())->toBe('yellow');
});

it('has correct symbols', function (): void {
    expect(DiagnosticStatus::Ok->symbol())->toBe('✓');
    expect(DiagnosticStatus::Failed->symbol())->toBe('✗');
    expect(DiagnosticStatus::Skipped->symbol())->toBe('~');
});
