<?php
// Extract favicon images from file path.
$faviconFile = __DIR__ . '/bbc.ico'
file_put_contents($faviconFile, file_get_contents('https://www.bbc.co.uk/favicon.ico'));

$imagick = new Imagick();
$imagick->readImage($faviconFile);
$imagick->writeImages(__DIR__ . '/bbc-extracted.png', false);

// Extract favicon images from string.
$imagick = new Imagick();
$imagick->setFormat('ICO');
$imagick->readImageBlob(file_get_contents('https://www.bbc.co.uk/favicon.ico'));
$imagick->writeImages(__DIR__ . '/bbc-extracted.png', false);