<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRegistry;

/**
 * @method static void register(Diagnostic|class-string<Diagnostic> $diagnostic)
 * @method static Collection<string, Diagnostic> all()
 * @method static Collection<string, Diagnostic> group(string $group)
 * @method static Diagnostic|null find(string $name)
 * @method static Collection<string, Diagnostic> findMany(array $names)
 * @method static bool has(string $name)
 * @method static void forget(string $name)
 *
 * @see DiagnosticRegistry
 */
final class Diagnostics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DiagnosticRegistry::class;
    }
}
