<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Phpmig\Console\Command\CheckCommand;
use Phpmig\Console\Command\DownCommand;
use Phpmig\Console\Command\GenerateCommand;
use Phpmig\Console\Command\InitCommand;
use Phpmig\Console\Command\MigrateCommand;
use Phpmig\Console\Command\RedoCommand;
use Phpmig\Console\Command\RollbackCommand;
use Phpmig\Console\Command\StatusCommand;
use Phpmig\Console\Command\UpCommand;

$console = new Application('Application', '1.0');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The environment name.', 'dev'));

$commands = array(
    new CheckCommand(),
    new DownCommand(),
    new GenerateCommand(),
    new InitCommand(),
    new MigrateCommand(),
    new RedoCommand(),
    new RollbackCommand(),
    new StatusCommand(),
    new UpCommand(),
);

foreach ($commands as $command) {
    $command->setName('database:' . $command->getName());
}

$console->addCommands($commands);

return $console;