<?php
// @see: https://stackoverflow.com/questions/21861825/quick-way-to-find-the-largest-array-in-a-multidimensional-array
$array = [
   ['foo', 'bar'],
   ['foo'],
   ['foo', 'bar', 'baz'],
];

echo count(max($array)) . PHP_EOL;
echo count(min($array)) . PHP_EOL;