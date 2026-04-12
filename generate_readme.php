#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Generate README.md from the til directory structure.
 *
 * For each topic directory, lists every entry with its title (from the git
 * commit message) and the date it was first committed. Run this after adding
 * new entries to keep the README in sync.
 *
 * Usage: php generate_readme.php
 */

const SKIP_DIRS = ['.git'];

const TOPIC_OVERRIDES = [
    'html-css'     => 'HTML & CSS',
    'ai-generated' => 'AI Generated',
    'php'          => 'PHP',
    'javascript'   => 'JavaScript',
    'go'           => 'Go',
    'sql'          => 'SQL',
    'mac'          => 'Mac',
    'docker'       => 'Docker',
];

$root = dirname(__FILE__);

function git(string ...$args): string
{
    global $root;

    $cmd = array_map('escapeshellarg', ['git', ...$args]);
    $output = shell_exec('cd ' . escapeshellarg($root) . ' && ' . implode(' ', $cmd));

    return trim($output ?? '');
}

function entryDate(string $path): string
{
    $log = git('log', '--diff-filter=A', '--follow', '--format=%as', '--', $path);
    $lines = array_filter(explode("\n", $log));

    return end($lines) ?: '';
}

function entryTitle(string $path): string
{
    $log = git('log', '--diff-filter=A', '--follow', '--format=%s', '--', $path);
    $lines = array_filter(explode("\n", $log));
    $msg = end($lines) ?: '';

    if (preg_match('/^til\([^)]+\):\s*(.+)/', $msg, $matches)) {
        return trim($matches[1]);
    }

    return ucwords(str_replace('-', ' ', basename($path)));
}

function topicTitle(string $name): string
{
    return TOPIC_OVERRIDES[$name] ?? ucwords(str_replace('-', ' ', $name));
}

/** @return array<SplFileInfo> */
function subdirs(string $path): array
{
    $dirs = array_filter(
        iterator_to_array(new FilesystemIterator($path)),
        fn(SplFileInfo $item) => $item->isDir() && !in_array($item->getFilename(), SKIP_DIRS),
    );

    usort($dirs, fn(SplFileInfo $a, SplFileInfo $b) => $a->getFilename() <=> $b->getFilename());

    return array_values($dirs);
}

/** @return array<SplFileInfo> */
function allFiles(string $path): array
{
    $files = array_filter(
        iterator_to_array(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path))),
        fn(SplFileInfo $item) => $item->isFile(),
    );

    usort($files, fn(SplFileInfo $a, SplFileInfo $b) => $a->getPathname() <=> $b->getPathname());

    return array_values($files);
}

$topics = subdirs($root);
$allEntries = [];
$topicSections = [];

foreach ($topics as $topicDir) {
    $entries = subdirs($topicDir->getPathname());

    if (empty($entries)) {
        continue;
    }

    $rows = [];

    foreach ($entries as $entry) {
        $files = allFiles($entry->getPathname());

        if (empty($files)) {
            continue;
        }

        $rel = ltrim(str_replace($root, '', $files[0]->getPathname()), '/');
        $date = entryDate($rel);
        $title = entryTitle($rel);
        $entryRel = ltrim(str_replace($root, '', $entry->getPathname()), '/');
        $rows[] = [$date, $title, $entryRel];
        $allEntries[] = [$date, $title, $entryRel, $topicDir->getFilename()];
    }

    usort($rows, fn(array $a, array $b) => $b[0] <=> $a[0]);

    $lines = ['## ' . topicTitle($topicDir->getFilename()), ''];
    foreach ($rows as [$date, $title, $rel]) {
        $lines[] = "- [{$title}]({$rel}/) — {$date}";
    }

    $topicSections[] = implode("\n", $lines);
}

$total = count($allEntries);
$topicCount = count($topicSections);

$tocLines = array_map(
    function (SplFileInfo $t) use ($root): string {
        $name = $t->getFilename();
        $count = count(subdirs($t->getPathname()));
        $anchor = $name;
        return "- [" . topicTitle($name) . "](#{$anchor}) ({$count})";
    },
    array_filter($topics, fn(SplFileInfo $t) => !empty(subdirs($t->getPathname()))),
);

$header = <<<MD
    # TIL

    > Things I've picked up, figured out, or found useful. Inspired by [simonw/til](https://github.com/simonw/til).

    {$total} entries across {$topicCount} topics.

    ## Topics

    MD;

$header = preg_replace('/^    /m', '', $header);
$header .= "\n" . implode("\n", $tocLines) . "\n";

$body = implode("\n\n", $topicSections);

file_put_contents($root . '/README.md', $header . "\n" . $body . "\n");

echo "README.md written: {$total} entries across {$topicCount} topics." . PHP_EOL;
