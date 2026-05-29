<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics;

use Illuminate\Support\Collection;
use InvalidArgumentException;

final class DiagnosticRegistry
{
    /** @var array<string, Diagnostic> */
    private array $diagnostics = [];

    public function register(Diagnostic|string $diagnostic): void
    {
        if (is_string($diagnostic)) {
            if (! class_exists($diagnostic) || ! is_subclass_of($diagnostic, Diagnostic::class)) {
                throw new InvalidArgumentException(
                    "Class [{$diagnostic}] must extend ".Diagnostic::class
                );
            }

            $diagnostic = new $diagnostic;
        }

        $this->diagnostics[$diagnostic->name()] = $diagnostic;
    }

    /**
     * @return Collection<string, Diagnostic>
     */
    public function all(): Collection
    {
        return collect($this->diagnostics)->sortKeys();
    }

    /**
     * Return all diagnostics belonging to a dot-prefix group, e.g. 'db' returns db.connect, db.query.
     *
     * @return Collection<string, Diagnostic>
     */
    public function group(string $group): Collection
    {
        $prefix = rtrim($group, '.').'.';

        return $this->all()->filter(
            fn (Diagnostic $d) => str_starts_with($d->name(), $prefix)
        );
    }

    public function find(string $name): ?Diagnostic
    {
        return $this->diagnostics[$name] ?? null;
    }

    /**
     * @param  string[]  $names
     * @return Collection<string, Diagnostic>
     */
    public function findMany(array $names): Collection
    {
        return collect($names)
            ->map(fn (string $name) => $this->find($name))
            ->filter()
            ->keyBy(fn (Diagnostic $d) => $d->name());
    }

    public function has(string $name): bool
    {
        return isset($this->diagnostics[$name]);
    }

    public function forget(string $name): void
    {
        unset($this->diagnostics[$name]);
    }
}
