<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Commands;

use Illuminate\Console\Command;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRegistry;

final class ListDiagnosticsCommand extends Command
{
    public $signature = 'diagnostics:list
                        {--group= : Filter by group prefix, e.g. db or disk}';

    public $description = 'List all registered diagnostics';

    public function handle(DiagnosticRegistry $registry): int
    {
        $group = $this->option('group');

        $diagnostics = $group !== null
            ? $registry->group($group)
            : $registry->all();

        if ($diagnostics->isEmpty()) {
            $this->warn('No diagnostics registered'.($group ? " in group [{$group}]" : '').'.');

            return self::SUCCESS;
        }

        $rows = $diagnostics->map(fn (Diagnostic $d) => [
            $d->name(),
            $d->label(),
            empty($d->environments()) ? 'all' : implode(', ', $d->environments()),
        ])->values()->all();

        $this->newLine();
        $this->table(['Name', 'Label', 'Environments'], $rows);
        $this->newLine();

        return self::SUCCESS;
    }
}
