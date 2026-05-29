<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Contracts;

use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

interface DiagnosticContract
{
    public function name(): string;

    public function label(): string;

    /**
     * The environments this diagnostic may run in.
     * An empty array means all environments are allowed.
     *
     * @return string[]
     */
    public function environments(): array;

    /**
     * Whether this diagnostic should be skipped in the current environment.
     * Can be overridden for custom skip logic.
     */
    public function shouldSkip(): bool;

    /**
     * Run the diagnostic check and return a result.
     *
     * @param  array<string, mixed>  $options
     */
    public function run(array $options = []): DiagnosticResult;
}
