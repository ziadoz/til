<?php
 
namespace App\Providers;

use App\YourCustomOutputStyle;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Extending the OutputStyle class within the Laravel container allows you to alter Artisan's output.
     * For example, you could wrap OutputStyle in a class that also logs the output for ephemeral environments such as Fortrabbit.
     */
    public function register()
    {
        $this->app->extend(OutputStyle::class, function (OutputStyle $output, $app) {
            return new YourCustomOutputStyle($output);
        });
    }
}