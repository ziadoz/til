<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics;

use Illuminate\Support\Benchmark;
use Ziadoz\LaravelDiagnostics\Contracts\DiagnosticContract;

abstract class Diagnostic implements DiagnosticContract
{
    abstract public function name(): string;

    abstract public function label(): string;

    abstract public function run(array $options = []): DiagnosticResult;

    public function environments(): array
    {
        return [];
    }

    public function shouldSkip(): bool
    {
        $environments = $this->environments();

        if (empty($environments)) {
            return false;
        }

        return ! app()->environment($environments);
    }

    final public function execute(array $options = []): DiagnosticResult
    {
        $result = $this->shouldSkip()
            ? DiagnosticResult::skipped('Not available in ['.app()->environment().'] environment')
            : $this->run($options);

        return $result->forDiagnostic($this->name(), $this->label());
    }

    /**
     * Benchmark a callable and return [result, durationMs].
     *
     * @return array{0: mixed, 1: float}
     */
    protected function benchmark(callable $callback): array
    {
        /** @var array{0: mixed, 1: float} */
        return Benchmark::measure($callback);
    }
}
