#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * Download forced (foreign-parts-only) English subtitles from OpenSubtitles.com.
 *
 * Credentials come from environment variables:
 *   OPENSUBTITLES_API_KEY  OPENSUBTITLES_USERNAME  OPENSUBTITLES_PASSWORD
 *
 * Usage:
 *   ./get-forced-subs.php "/path/Show - S01E01 - Title.mp4" [more...]
 *
 * Saves "<video-basename>.srt" next to each file; existing .srt files are skipped.
 */

/**
 * An episode parsed from a "Show - S01E01 - Title" style filename.
 */
final readonly class Episode
{
    public function __construct(
        public string $title,
        public int $season,
        public int $number,
    ) {}

    public static function fromFilename(string $name): ?self
    {
        if (! preg_match('/[Ss](\d{1,2})[Ee](\d{1,3})/', $name, $matches)) {
            return null;
        }

        return new self(
            title: preg_split('/ - [Ss]\d/', $name)[0],
            season: (int) $matches[1],
            number: (int) $matches[2],
        );
    }

    public function code(): string
    {
        return sprintf('S%02dE%02d', $this->season, $this->number);
    }
}

/**
 * Thin client for the OpenSubtitles.com REST API.
 */
final class OpenSubtitlesClient
{
    private const BASE_URL = 'https://api.opensubtitles.com/api/v1';

    private const USER_AGENT = 'get-forced-subs/1.0';

    private readonly string $token;

    private readonly string $downloadUrl;

    public function __construct(
        private readonly string $apiKey,
        string $username,
        string $password,
    ) {
        $login = $this->send('POST', self::BASE_URL.'/login', [
            'username' => $username,
            'password' => $password,
        ]);

        $this->token = $login['token']
            ?? throw new RuntimeException('Login failed: '.json_encode($login));

        $this->downloadUrl = empty($login['base_url'])
            ? self::BASE_URL
            : "https://{$login['base_url']}/api/v1";
    }

    /**
     * Search for forced English subtitles for a single episode.
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchForcedEnglish(string $query, int $season, int $episode, ?string $movieHash): array
    {
        $params = array_filter([
            'type' => 'episode',
            'languages' => 'en',
            'foreign_parts_only' => 'only',
            'query' => $query,
            'season_number' => $season,
            'episode_number' => $episode,
            'moviehash' => $movieHash,
        ], fn ($value) => $value !== null);

        return $this->send('GET', self::BASE_URL.'/subtitles?'.http_build_query($params))['data'] ?? [];
    }

    /**
     * Ask for a temporary download link and the account's remaining quota.
     *
     * @return array{link: ?string, remaining: int|string}
     */
    public function requestDownload(int $fileId): array
    {
        $response = $this->send('POST', "{$this->downloadUrl}/download", [
            'file_id' => $fileId,
            'sub_format' => 'srt',
        ], authenticated: true);

        return [
            'link' => $response['link'] ?? null,
            'remaining' => $response['remaining'] ?? '?',
        ];
    }

    /**
     * Stream a subtitle link to disk, writing to a temp file first so a failed
     * transfer never leaves a broken .srt behind.
     */
    public function saveSubtitle(string $link, string $destination): void
    {
        $temp = $destination.'.part';
        $file = fopen($temp, 'wb');

        $curl = curl_init($link);
        curl_setopt_array($curl, [
            CURLOPT_FILE => $file,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_USERAGENT => self::USER_AGENT,
        ]);

        $ok = curl_exec($curl);
        fclose($file);

        if ($ok === false) {
            @unlink($temp);

            throw new RuntimeException('Failed to fetch subtitle content.');
        }

        rename($temp, $destination);
    }

    /**
     * OpenSubtitles file hash: size + 64-bit LE checksum of the first and last
     * 64 KiB, computed with 32-bit halves and a carry to stay exact without GMP.
     */
    public static function hashFile(string $path): ?string
    {
        $size = filesize($path);

        if ($size < 2 * 65536) {
            return null;
        }

        $low = $size & 0xFFFFFFFF;
        $high = ($size >> 32) & 0xFFFFFFFF;

        $add = function (int $lowWord, int $highWord) use (&$low, &$high): void {
            $low += $lowWord;
            $high = ($high + $highWord + ($low >> 32)) & 0xFFFFFFFF;
            $low &= 0xFFFFFFFF;
        };

        $handle = fopen($path, 'rb');

        $sumChunk = function () use ($handle, $add): void {
            for ($i = 0; $i < 65536 / 8; $i++) {
                [1 => $lowWord, 2 => $highWord] = unpack('V2', fread($handle, 8));
                $add($lowWord, $highWord);
            }
        };

        $sumChunk();
        fseek($handle, $size - 65536);
        $sumChunk();
        fclose($handle);

        return sprintf('%08x%08x', $high, $low);
    }

    private function send(string $method, string $url, ?array $body = null, bool $authenticated = false): array
    {
        $headers = [
            "Api-Key: {$this->apiKey}",
            'Accept: application/json',
            'User-Agent: '.self::USER_AGENT,
        ];

        if ($authenticated) {
            $headers[] = "Authorization: Bearer {$this->token}";
        }

        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true, // the API 301-redirects to a canonical URL
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body === null ? null : json_encode($body),
        ]);

        $response = curl_exec($curl);

        return json_decode((string) $response, true) ?? [];
    }
}

/**
 * Fetches the forced English subtitle for one video file and reports progress.
 */
final class ForcedSubtitleDownloader
{
    public function __construct(private readonly OpenSubtitlesClient $client) {}

    public function handle(string $path): void
    {
        printf("=== %s ===\n", basename($path));

        if (! is_file($path)) {
            echo "  Not found, skipping.\n";

            return;
        }

        $destination = preg_replace('/\.[^.]+$/', '', $path).'.srt';

        if (file_exists($destination)) {
            echo "  Already have: {$destination}\n";

            return;
        }

        $episode = Episode::fromFilename(pathinfo($path, PATHINFO_FILENAME));

        if ($episode === null) {
            echo "  Cannot find SxxExx, skipping.\n";

            return;
        }

        $results = $this->client->searchForcedEnglish(
            $episode->title,
            $episode->season,
            $episode->number,
            OpenSubtitlesClient::hashFile($path),
        );

        $match = $this->bestMatch($results, $episode->title);

        if ($match === null) {
            $this->reportNoMatch($results, $episode);

            return;
        }

        echo "  Match: {$match['release']}\n";

        ['link' => $link, 'remaining' => $remaining] = $this->client->requestDownload($match['file_id']);

        if ($link === null) {
            echo "  Download request failed.\n";

            return;
        }

        $this->client->saveSubtitle($link, $destination);
        echo "  Saved: {$destination}  (remaining today: {$remaining})\n";
    }

    /**
     * Keep only results whose parent show title matches this file's title (so a
     * same-word show can never be picked), preferring an exact moviehash match.
     *
     * @param  array<int, array<string, mixed>>  $results
     * @return array{file_id: int, release: string}|null
     */
    private function bestMatch(array $results, string $title): ?array
    {
        $wanted = $this->normalise($title);

        $matches = array_values(array_filter(
            $results,
            fn (array $result) => $this->normalise($result['attributes']['feature_details']['parent_title'] ?? '') === $wanted,
        ));

        usort(
            $matches,
            fn (array $a, array $b) => ($b['attributes']['moviehash_match'] ?? false) <=> ($a['attributes']['moviehash_match'] ?? false),
        );

        $best = $matches[0]['attributes'] ?? null;

        if ($best === null) {
            return null;
        }

        return [
            'file_id' => $best['files'][0]['file_id'],
            'release' => $best['release'] ?: (string) $best['files'][0]['file_id'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     */
    private function reportNoMatch(array $results, Episode $episode): void
    {
        $otherShows = array_values(array_unique(array_map(
            fn (array $result) => $result['attributes']['feature_details']['parent_title'] ?? '?',
            $results,
        )));

        echo $otherShows === []
            ? "  No forced English subtitle found for {$episode->code()}.\n"
            : sprintf(
                "  No forced English subtitle for \"%s\" %s (ignored: %s).\n",
                $episode->title,
                $episode->code(),
                implode(', ', $otherShows),
            );
    }

    private function normalise(string $value): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($value));
    }
}

$credentials = [];

foreach (['OPENSUBTITLES_API_KEY', 'OPENSUBTITLES_USERNAME', 'OPENSUBTITLES_PASSWORD'] as $variable) {
    $value = getenv($variable);

    if ($value === false || $value === '') {
        fwrite(STDERR, "Set {$variable}\n");
        exit(1);
    }

    $credentials[$variable] = $value;
}

$files = array_slice($argv, 1);

if ($files === []) {
    fwrite(STDERR, "Usage: {$argv[0]} <video-file> [more...]\n");
    exit(1);
}

try {
    $client = new OpenSubtitlesClient(
        $credentials['OPENSUBTITLES_API_KEY'],
        $credentials['OPENSUBTITLES_USERNAME'],
        $credentials['OPENSUBTITLES_PASSWORD'],
    );
} catch (RuntimeException $exception) {
    fwrite(STDERR, $exception->getMessage()."\n");
    exit(1);
}

echo "Logged in as {$credentials['OPENSUBTITLES_USERNAME']}\n";

$downloader = new ForcedSubtitleDownloader($client);

foreach ($files as $file) {
    try {
        $downloader->handle($file);
    } catch (RuntimeException $exception) {
        fwrite(STDERR, "  {$exception->getMessage()}\n");
    }
}
