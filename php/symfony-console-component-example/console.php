#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

$console = new Application('Example CLI', '1.0');

/**
 * Say - Hello.
 */
$console->register('say:hello')
        ->setDescription('Say hello to someone on the command line.')
        ->setDefinition(array(
            new InputArgument('name', InputArgument::OPTIONAL, 'The name of the person to say hello to.', 'Stranger'),
        ))
        ->setCode(function (InputInterface $input, OutputInterface $output) {
            $name = ucwords($input->getArgument('name'));
            $output->writeln('Hello, ' . $name . '!');  
        });

/**
 * List - Files.
 */
$console->register('files:list')
        ->setDescription('List all the files in a given directory.')
        ->setDefinition(array(
            new InputArgument('dir', InputArgument::REQUIRED, 'The directory to search for files in.'),
            new InputOption('ext', 'e', InputOption::VALUE_OPTIONAL , 'An optional file extension to filter by.'),
        ))
        ->setCode(function(InputInterface $input, OutputInterface $output) {
            $dir = $input->getArgument('dir');
            if (! file_exists($dir) || ! is_dir($dir)) {
                throw new InvalidArgumentException('The directory must exist.');
            }

            $ext = strtolower($input->getOption('ext'));
            if (! empty($ext)) {
                $ext = '*.' . str_replace(array('*', '.'), '', $ext);
            }

            $finder = new Finder;
            $finder->in($dir);
            $finder->sortByName();

            if ($ext) {
                $finder->files()->name($ext);
            }

            $depthSep   = '   ';
            $subDepth   = 0;
            $startDepth = (substr_count($dir, DIRECTORY_SEPARATOR) - 1);

            $output->writeln('<info>' . $dir . ': </info>');
            foreach ($finder as $file) {
                if ($file->isDir()) {
                    $subDepth = (substr_count($file->getPathname(), DIRECTORY_SEPARATOR) - $startDepth);
                    $output->writeln('<info>' . str_repeat($depthSep, $subDepth) . ' ' . trim($file->getFilename()) . '</info>');
                } else {
                    $output->writeln(str_repeat($depthSep, $subDepth + 1) . trim($file->getFilename()));
                }
            }
        });

$console->run();

/**
 * Usage:
 *
 * php console.php say:hello
 * php console.php say:hello Foobar
 *
 * php console.php files:list ~/Downloads
 * php console.php files:list ~/Downloads --ext=php
 */