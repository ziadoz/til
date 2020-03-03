<?php
// app/Console/Kernel.php
namespace App\Console;

use Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\{GenerateHydratorsCommand, GeneratePersistentCollectionsCommand, GenerateProxiesCommand, QueryCommand};
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\{CreateCommand, DropCommand, ShardCommand, UpdateCommand, ValidateCommand};
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
        
        // Registering these commands in the testing environment probably isn't going to help your memory usage, so don't bother.
        if (env('APP_ENV') !== 'testing') {
            $this->registerDoctrineCommands(); 
        }
    }

    /**
     * Register all Doctrine Mongo ODM Symfony commands so they're available to call through Artisan.
     */
    protected function registerDoctrineCommands()
    {
        $commands = [
            new MetadataCommand,
            new CreateCommand,
            new DropCommand,
            new ShardCommand,
            new UpdateCommand,
            new ValidateCommand,
            new GenerateHydratorsCommand,
            new GeneratePersistentCollectionsCommand,
            new GenerateProxiesCommand,
            new QueryCommand,
        ];

        // Add DocumentManager to existing Artisan/Symfony HelperSet as commands need it.
        $helperSet = $this->getArtisan()->getHelperSet();
        $helperSet->set(new DocumentManagerHelper(App::get('doctrine.dm'))); // Retrieve your DocumentManager here however it's configured.
        $this->getArtisan()->setHelperSet($helperSet);

        // Register each command.
        foreach ($commands as $command) {
            $command->setHelperSet($helperSet);
            $this->registerCommand($command);
        }
    }
}
