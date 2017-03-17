<?php
$returned = include __DIR__ . '/include-me-unset.php';
echo (isset($array) ? '$array variable is set' : '$array variable is not set') . PHP_EOL;
echo (isset($string) ? '$string variable is set' : '$string variable is not set') . PHP_EOL;
echo '$returned is ' . $returned . PHP_EOL;

$returned = include __DIR__ . '/include-me-func.php';
echo (isset($array) ? '$array variable is set' : '$array variable is not set') . PHP_EOL;
echo (isset($string) ? '$string variable is set' : '$string variable is not set') . PHP_EOL;
echo '$returned is ' . $returned . PHP_EOL;

$returned = (function () { return include __DIR__ . '/include-me-leaky.php'; })();
echo (isset($array) ? '$array variable is set' : '$array variable is not set') . PHP_EOL;
echo (isset($string) ? '$string variable is set' : '$string variable is not set') . PHP_EOL;
echo '$returned is ' . $returned . PHP_EOL;