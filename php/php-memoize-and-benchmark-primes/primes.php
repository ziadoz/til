<?php
$prime = function($num) {
    if ($num === 1) {
        return false;
    }

    if ($num === 2) {
        return true;
    }

    if ($num % 2 === 0) {
        return false;
    }

    $total = ceil(sqrt($num));
    for ($i = 3; $i < $total; $i = $i + 2) {
        if ($num % $i === 0) {
            return false;
        }
    }

    return true;
};

$range = function($start, $end, $function) {
    return function() use ($start, $end, $function) {
        $results = array();
        for ($i = $start; $i <= $end; $i++) {
            if ($function($i) === true) {
                $results[] = $i;
            }
        }

        return $results;
    };
};

$memoize = function($function) {
    static $cache = array();

    return function() use (&$cache, $function) {
        $args   = func_get_args();
        $md5    = md5(implode('', $args));

        if (isset($cache[$md5])) {
            return $cache[$md5];
        }

        $cache[$md5] = call_user_func_array($function, $args);
        return $cache[$md5];
    };
};

$benchmark = function($function) {
    return function() use ($function) {
        $start  = microtime(true);
        $result = call_user_func_array($function, func_get_args());
        $end    = microtime(true);

        echo 'Time Elapsed: ' . ($end - $start) . "\n";
        return $result;
    };
};

echo '<pre>';

$start  = 1;
$end    = 1000;

$primes = $benchmark($range($start, $end, $memoize($prime)));
print_r($primes());

$primes = $benchmark($range($start, $end, $memoize($prime)));
print_r($primes());

echo '</pre>';