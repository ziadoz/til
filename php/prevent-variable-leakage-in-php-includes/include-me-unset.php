<?php
$array = ['foo', 'bar'];

foreach ($array as $string) {
    // Some exciting logic.
}

unset($array, $string); // Unset variables so nothing leaks out when included.

return 'FOOBAR';