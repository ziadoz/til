<?php

declare(strict_types=1);

use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRegistry;

it('lists all registered diagnostics', function (): void {
    $this->artisan('diagnostics:list')
        ->assertSuccessful()
        ->expectsOutputToContain('db.connect');
});

it('filters diagnostics by group using the registry group method', function (): void {
    $this->artisan('diagnostics:list', ['--group' => 'db'])
        ->assertSuccessful()
        ->expectsOutputToContain('db.connect')
        ->expectsOutputToContain('db.query');
});

it('warns when no diagnostics match the group filter', function (): void {
    $this->artisan('diagnostics:list', ['--group' => 'nonexistent'])
        ->assertSuccessful()
        ->expectsOutputToContain('No diagnostics registered');
});

it('warns when no diagnostics are registered', function (): void {
    $registry = $this->app->make(DiagnosticRegistry::class);

    foreach ($registry->all() as $diagnostic) {
        $registry->forget($diagnostic->name());
    }

    $this->artisan('diagnostics:list')
        ->assertSuccessful()
        ->expectsOutputToContain('No diagnostics registered');
});
