#!/usr/bin/env php
<?php

declare(strict_types=1);

enum Source: string {
    case ReadingList = 'reading_list';
    case LocalTab    = 'local_tab';
    case ICloudTab   = 'icloud_tab';
}

readonly class Tab {
    public function __construct(
        public Source $source,
        public string $device,
        public string $title,
        public string $url,
        public string $dateAdded,
        public string $read,
    ) {}

    public function toArray(): array {
        return [
            'source'     => $this->source->value,
            'device'     => $this->device,
            'title'      => $this->title,
            'url'        => $this->url,
            'date_added' => $this->dateAdded,
            'read'       => $this->read,
        ];
    }
}

// --- Plist XML parser ---

function parsePlist(DOMNode $node): mixed {
    return match ($node->nodeName) {
        'plist'   => parsePlist(firstElement($node)),
        'dict'    => parsePlistDict($node),
        'array'   => parsePlistArray($node),
        'string'  => $node->textContent,
        'integer' => (int) $node->textContent,
        'real'    => (float) $node->textContent,
        'date'    => $node->textContent,
        'true'    => true,
        'false'   => false,
        default   => null,
    };
}

function firstElement(DOMNode $parent): DOMNode {
    foreach ($parent->childNodes as $child) {
        if ($child->nodeType === XML_ELEMENT_NODE) {
            return $child;
        }
    }
    throw new RuntimeException('No element child found in plist node');
}

function parsePlistDict(DOMNode $node): array {
    $result = [];
    $key    = null;
    foreach ($node->childNodes as $child) {
        if ($child->nodeType !== XML_ELEMENT_NODE) {
            continue;
        }
        if ($child->nodeName === 'key') {
            $key = $child->textContent;
        } elseif ($key !== null) {
            $result[$key] = parsePlist($child);
            $key = null;
        }
    }
    return $result;
}

function parsePlistArray(DOMNode $node): array {
    $result = [];
    foreach ($node->childNodes as $child) {
        if ($child->nodeType === XML_ELEMENT_NODE) {
            $result[] = parsePlist($child);
        }
    }
    return $result;
}

function loadPlist(string $path): array {
    $xml = shell_exec('plutil -convert xml1 -o - ' . escapeshellarg($path));
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    return parsePlist($dom->documentElement);
}

// --- Helpers ---

function findNode(array $node, string $title): ?array {
    if (($node['Title'] ?? '') === $title) {
        return $node;
    }
    foreach ($node as $value) {
        if (is_array($value)) {
            $result = findNode($value, $title);
            if ($result !== null) {
                return $result;
            }
        }
    }
    return null;
}

function isoDate(string $date): string {
    if ($date === '') {
        return '';
    }
    return (new DateTimeImmutable($date))->format('Y-m-d H:i:s');
}

function appleTimestamp(float|null $ts): string {
    if ($ts === null || $ts === 0.0) {
        return '';
    }
    return (new DateTimeImmutable('@' . (int) ($ts + 978_307_200)))->format('Y-m-d H:i:s');
}

// --- Data sources ---

/** @return Tab[] */
function readingList(string $path): array {
    if (!file_exists($path)) {
        fwrite(STDERR, "Warning: {$path} not found\n");
        return [];
    }

    $node = findNode(loadPlist($path), 'com.apple.ReadingList');
    if ($node === null) {
        return [];
    }

    return array_map(fn(array $item): Tab => new Tab(
        source:    Source::ReadingList,
        device:    '',
        title:     $item['URIDictionary']['title'] ?? '',
        url:       $item['URLString'] ?? '',
        dateAdded: isoDate($item['ReadingList']['DateAdded'] ?? ''),
        read:      isset($item['ReadingList']['DateLastFetched']) ? 'yes' : 'no',
    ), $node['Children'] ?? []);
}

/** @return Tab[] */
function localTabs(): array {
    $script = <<<'APPLESCRIPT'
        tell application "Safari"
            set output to ""
            repeat with w in windows
                repeat with t in tabs of w
                    set output to output & (name of t) & "|||" & (URL of t) & linefeed
                end repeat
            end repeat
            return output
        end tell
    APPLESCRIPT;

    $output = shell_exec('osascript -e ' . escapeshellarg($script));
    if ($output === null) {
        fwrite(STDERR, "Warning: AppleScript failed\n");
        return [];
    }

    $tabs = [];
    foreach (explode("\n", trim($output)) as $line) {
        if (!str_contains($line, '|||')) {
            continue;
        }
        [$title, $url] = explode('|||', $line, 2);
        if ($url === '' || $url === 'favorites://') {
            continue;
        }
        $tabs[] = new Tab(
            source:    Source::LocalTab,
            device:    'this mac',
            title:     $title,
            url:       $url,
            dateAdded: '',
            read:      '',
        );
    }
    return $tabs;
}

/** @return Tab[] */
function icloudTabs(string $path): array {
    if (!file_exists($path)) {
        fwrite(STDERR, "Warning: {$path} not found\n");
        return [];
    }

    $pdo = new PDO('sqlite:' . $path, options: [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    ]);

    $rows = $pdo->query(<<<'SQL'
        SELECT t.title, t.url, t.last_viewed_time, d.device_name
        FROM cloud_tabs t
        LEFT JOIN cloud_tab_devices d ON t.device_uuid = d.device_uuid
        WHERE t.url IS NOT NULL AND t.url != ''
        ORDER BY d.device_name, t.last_viewed_time DESC
    SQL)->fetchAll();

    return array_map(fn(array $row): Tab => new Tab(
        source:    Source::ICloudTab,
        device:    $row['device_name'] ?? '',
        title:     $row['title'] ?? '',
        url:       $row['url'],
        dateAdded: appleTimestamp($row['last_viewed_time'] ?? null),
        read:      '',
    ), $rows);
}

function writeCsv(string $path, array $tabs): void {
    $fh = fopen($path, 'w');
    fputcsv($fh, ['source', 'device', 'title', 'url', 'date_added', 'read'], escape: '');
    array_walk($tabs, fn(Tab $tab) => fputcsv($fh, $tab->toArray(), escape: ''));
    fclose($fh);
}

// --- Main ---

$home   = $_SERVER['HOME'];
$output = "{$home}/Desktop/safari_export.csv";

echo "Exporting Safari data...\n";

$reading = readingList("{$home}/Library/Safari/Bookmarks.plist");
printf("  Reading list: %d items\n", count($reading));

$local = localTabs();
printf("  Local tabs:   %d items\n", count($local));

$icloud = icloudTabs("{$home}/Library/Containers/com.apple.Safari/Data/Library/Safari/CloudTabs.db");
printf("  iCloud tabs:  %d items\n", count($icloud));

$all = [...$reading, ...$local, ...$icloud];
writeCsv($output, $all);

printf("\nWritten %d rows to %s\n", count($all), $output);
