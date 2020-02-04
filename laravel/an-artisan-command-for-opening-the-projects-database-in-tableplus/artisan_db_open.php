<?php

Artisan::command('db:open {connection?}', function ($connection = null) {
    if (! file_exists('/Applications/TablePlus.app')) {
        $this->warn('This command uses TablePlus, are you sure it\'s installed?');
        $this->line("Install here: https://tableplus.com/\n");
    }

    $driver = $connection ?: config('database.default');
    $host = config("database.connections.{$driver}.host");
    $user = config("database.connections.{$driver}.username");
    $password = config("database.connections.{$driver}.password");
    $database = config("database.connections.{$driver}.database");

    if ($driver === 'sqlite') {
        exec("open {$database}");
    } else {
        exec("open {$driver}://{$user}:{$password}@{$host}/{$database}");
    }
});