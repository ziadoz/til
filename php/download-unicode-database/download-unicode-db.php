<?php
// Download Unicode database CSV:
$ch = curl_init();

curl_setopt_array($ch, $options = [
    CURLOPT_URL            => 'https://www.unicode.org/Public/UCD/latest/ucd/UnicodeData.txt',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_FAILONERROR    => true,
]);

$data = curl_exec($ch);

if (! $data) {
    echo 'Unable to download Unicode database: ' . curl_error($ch);
    exit(1);
}

curl_close($ch);

file_put_contents(__DIR__ . '/UnicodeData.txt', $data);

// Format Unicode database as JSON:
$json = [];

$csv = fopen(__DIR__ . '/UnicodeData.txt', 'rb');

while (($row = fgetcsv($csv, null, ';', '"', '')) !== false) {
    $json[$row[0]] = $row[1];
}

fclose($csv);

$object = json_encode($json, JSON_PRETTY_PRINT);

echo <<<JS
export const database = $object;
JS;
