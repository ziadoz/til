<?php
// @see: https://stackoverflow.com/questions/2189479/get-the-maximum-value-from-an-element-in-a-multidimensional-array
$array = [
	'foo' => [1, 2, 3],
	'bar' => [1],
	'baz' => [1, 2],
];

echo count(max($array)); // 3
