<?php
// Add a binding to the container for Laravel's OutputStyle class, which is the default Artisan command output.
// Then replace NullOutput with your custom Output class.

$this->app->bind(OutputStyle::class, function () {
    return new OutputStyle(new ArgvInput(), new NullOutput());
});