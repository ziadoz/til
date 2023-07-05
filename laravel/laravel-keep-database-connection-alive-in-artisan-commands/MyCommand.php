<?php

namespace App\Console\Commands;

class MyCommand extends Command
{
    protected $signature = 'app:my-command';

    public function handle(): int
    { 
        // Ensure the MySQL connection stays alive during long running migrations.
        // @see: https://twitter.com/lyrixx/status/1575127719624544258
        $this->trap(SIGALRM, function (): void {
            DB::statement('SELECT 1');
            pcntl_alarm(30);
        });

        pcntl_alarm(30);
        
        // DB::statement('RUN SOME LONG OR SLOW SQL...');
        
        return self::SUCCESS;
    }
}