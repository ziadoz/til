<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics;

use Illuminate\Support\Collection;

final class DiagnosticRunner
{
    public function run(Diagnostic $diagnostic, array $options = []): DiagnosticResult
    {
        return $diagnostic->execute($options);
    }

    /**
     * @param  Diagnostic[]|Collection<string, Diagnostic>  $diagnostics
     * @param  array<string, mixed>  $options
     * @return Collection<int, DiagnosticResult>
     */
    public function runMany(array|Collection $diagnostics, array $options = []): Collection
    {
        return collect($diagnostics)
            ->map(fn (Diagnostic $diagnostic) => $this->run($diagnostic, $options));
    }
}
