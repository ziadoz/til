<?php

declare(strict_types=1);

namespace App\Console\Commands\Fix;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

// Artisan command to renumber duplicate (tenant_id, number) rows before
// the unique constraint migration runs. Uses window functions via fromSub()
// to rank duplicates and assign new numbers counting up from the tenant max.
//
// php artisan fix:duplicate-statement-numbers --dry-run
// php artisan fix:duplicate-statement-numbers

class FixDuplicateStatementNumbers extends Command
{
    protected $signature = 'fix:duplicate-statement-numbers
                            {--dry-run : Preview changes without applying them}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Find and repair duplicate statement numbers.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $fixes  = $this->buildFixes('statements');

        if ($fixes->isEmpty()) {
            info('No duplicate statement numbers found. Nothing to do.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            warning('Dry run — no changes will be applied.');
        }

        table(
            headers: ['Tenant', 'ID', 'Old #', 'New #'],
            rows: $fixes->map(fn (array $fix) => [
                sprintf('%s [%d]', $fix['tenant_name'], $fix['tenant_id']),
                $fix['id'],
                $fix['old_number'],
                $fix['new_number'],
            ]),
        );

        if ($dryRun) {
            info("{$fixes->count()} record(s) would be repaired.");

            return self::SUCCESS;
        }

        $confirmed = $this->option('force') || confirm(
            label: "Apply these {$fixes->count()} repair(s)?",
            default: false,
        );

        if (! $confirmed) {
            warning('Aborted.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($fixes): void {
            foreach ($fixes as $fix) {
                DB::table('statements')
                    ->where('id', $fix['id'])
                    ->update(['number' => $fix['new_number']]);
            }
        });

        info('Done.');
        info("{$fixes->count()} record(s) repaired across {$fixes->pluck('tenant_id')->unique()->count()} tenant(s).");

        return self::SUCCESS;
    }

    private function buildFixes(string $table): Collection
    {
        $tenantIds = DB::table($table)
            ->select('tenant_id')
            ->groupBy('tenant_id', 'number')
            ->havingRaw('COUNT(*) > 1')
            ->distinct()
            ->pluck('tenant_id');

        $tenants = Tenant::query()
            ->findMany($tenantIds)
            ->keyBy('id');

        $ranked = DB::table($table)
            ->selectRaw(
                'id, tenant_id, number, '
                . 'ROW_NUMBER() OVER (PARTITION BY tenant_id, number ORDER BY id) AS rank_number, '
                . 'MAX(number) OVER (PARTITION BY tenant_id) AS max_number'
            )
            ->whereIn('tenant_id', $tenantIds);

        return DB::query()
            ->fromSub($ranked, 'ranked')
            ->selectRaw(
                'id, tenant_id, number AS old_number, '
                . 'max_number + ROW_NUMBER() OVER (PARTITION BY tenant_id ORDER BY id) AS new_number'
            )
            ->where('rank_number', '>', 1)
            ->get()
            ->map(fn (object $row) => [
                'tenant_id'   => $row->tenant_id,
                'tenant_name' => $tenants->get($row->tenant_id)?->name ?? 'Unknown',
                'id'          => $row->id,
                'old_number'  => $row->old_number,
                'new_number'  => $row->new_number,
            ]);
    }
}
