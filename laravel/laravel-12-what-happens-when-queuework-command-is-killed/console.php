<?php

// In a Terminal tab run:
//   > php artisan scratch && php artisan queue:work --queue=test
//
// In a new Terminal tab run:
//   > kill -[NUM] $(pidof -s php artisan queue:work)
//
// Results:
// -2 (SIGINT) = Job continues looping.
// -3 (SIGQUIT) = Job continues looping.
// -15 (SIGTERM) = Job continues looping.
// -9 (Fatal Uncatchable Kill) = Everything dies, job is left in table as reserved.

class TestJob implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use \Illuminate\Foundation\Queue\Queueable;

    public int $timeout = 240; // More time than we're sleeping for...

    public function __construct()
    {
        $this->onQueue('test');
    }

    public function handle(): void
    {
        foreach (range(0, 60) as $loop) {
            logger()->debug('Job is running...', ['loop' => $loop]);
            DB::select('SELECT 1');
            sleep(5);
        }
    }
}

Artisan::command('scratch', function () {
    collect(['cache', 'cache_locks', 'jobs', 'failed_jobs', 'job_batches'])->each(fn ($table) => DB::connection('mysql-elevated')->table($table)->truncate());
    \TestJob::dispatch();
});
