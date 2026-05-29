<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRegistry;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRunner;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticStatus;

final class RunDiagnosticsCommand extends Command
{
    public $signature = 'diagnostics:run
                        {names?* : Diagnostic name(s) to run, e.g. db.connect disk.write}
                        {--connection= : Database or queue connection name}
                        {--disk= : Filesystem disk name}
                        {--store= : Cache store name}
                        {--mailer= : Mail mailer name}
                        {--url= : URL for the HTTP outbound check}
                        {--format=short : Output format: short, verbose, or json}';

    public $description = 'Run application diagnostics';

    public function handle(DiagnosticRegistry $registry, DiagnosticRunner $runner): int
    {
        $names = $this->argument('names');
        $format = $this->option('format');

        $diagnostics = ! empty($names)
            ? $registry->findMany($names)
            : $registry->all();

        if ($diagnostics->isEmpty()) {
            $this->error('No diagnostics found.');

            return self::FAILURE;
        }

        $options = array_filter([
            'connection' => $this->option('connection'),
            'disk' => $this->option('disk'),
            'store' => $this->option('store'),
            'mailer' => $this->option('mailer'),
            'url' => $this->option('url'),
        ]);

        $results = $runner->runMany($diagnostics, $options);

        match ($format) {
            'json' => $this->outputJson($results),
            'verbose' => $this->outputVerbose($results),
            default => $this->outputShort($results),
        };

        return $results->contains(fn (DiagnosticResult $r) => $r->status === DiagnosticStatus::Failed)
            ? self::FAILURE
            : self::SUCCESS;
    }

    /** @param Collection<int, DiagnosticResult> $results */
    private function outputShort(Collection $results): void
    {
        $this->newLine();

        $nameWidth = $results->max(fn (DiagnosticResult $r) => mb_strlen($r->name)) + 2;

        foreach ($results as $result) {
            $status = $result->status;
            $duration = $result->durationMs !== null
                ? ' <fg=gray>('.number_format($result->durationMs, 1).'ms)</>'
                : '';

            $this->line(sprintf(
                ' <fg=%s>%s</> %-'.$nameWidth.'s %s%s',
                $status->color(),
                $status->symbol(),
                $result->name,
                $result->message,
                $duration,
            ));
        }

        $this->newLine();
        $this->outputSummary($results);
    }

    /** @param Collection<int, DiagnosticResult> $results */
    private function outputVerbose(Collection $results): void
    {
        $this->newLine();

        foreach ($results as $result) {
            $status = $result->status;

            $this->line(sprintf(
                ' <fg=%s;options=bold>%s %s</>',
                $status->color(),
                $status->symbol(),
                $result->name,
            ));
            $this->line("   <fg=gray>Label:</>    {$result->label}");
            $this->line("   <fg=gray>Status:</>   <fg={$status->color()}>{$status->label()}</>");
            $this->line("   <fg=gray>Message:</> {$result->message}");

            if ($result->durationMs !== null) {
                $this->line('   <fg=gray>Duration:</> '.number_format($result->durationMs, 2).'ms');
            }

            if ($result->rawOutput !== null) {
                $this->line('   <fg=gray>Raw:</>');
                foreach (explode("\n", $result->rawOutput) as $line) {
                    $this->line("     {$line}");
                }
            }

            if (! empty($result->context)) {
                $this->line('   <fg=gray>Context:</>');
                foreach ($result->context as $key => $value) {
                    $this->line("     {$key}: {$value}");
                }
            }

            $this->newLine();
        }

        $this->outputSummary($results);
    }

    /** @param Collection<int, DiagnosticResult> $results */
    private function outputJson(Collection $results): void
    {
        $ok = $results->filter(fn (DiagnosticResult $r) => $r->status === DiagnosticStatus::Ok)->count();
        $failed = $results->filter(fn (DiagnosticResult $r) => $r->status === DiagnosticStatus::Failed)->count();
        $skipped = $results->filter(fn (DiagnosticResult $r) => $r->status === DiagnosticStatus::Skipped)->count();

        $output = [
            'ran_at' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'summary' => [
                'total' => $results->count(),
                'ok' => $ok,
                'failed' => $failed,
                'skipped' => $skipped,
            ],
            'results' => $results->map(fn (DiagnosticResult $r) => [
                'name' => $r->name,
                'label' => $r->label,
                'status' => $r->status->value,
                'message' => $r->message,
                'duration_ms' => $r->durationMs,
                'raw_output' => $r->rawOutput,
                'context' => $r->context,
            ])->values()->all(),
        ];

        $this->output->writeln((string) json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /** @param Collection<int, DiagnosticResult> $results */
    private function outputSummary(Collection $results): void
    {
        $parts = [];

        $ok = $results->filter(fn (DiagnosticResult $r) => $r->status === DiagnosticStatus::Ok)->count();
        $failed = $results->filter(fn (DiagnosticResult $r) => $r->status === DiagnosticStatus::Failed)->count();
        $skipped = $results->filter(fn (DiagnosticResult $r) => $r->status === DiagnosticStatus::Skipped)->count();

        if ($ok > 0) {
            $parts[] = "<fg=green>{$ok} passed</>";
        }

        if ($failed > 0) {
            $parts[] = "<fg=red>{$failed} failed</>";
        }

        if ($skipped > 0) {
            $parts[] = "<fg=yellow>{$skipped} skipped</>";
        }

        $this->line(' '.implode(', ', $parts));
        $this->newLine();
    }
}
