<?php
// @see: https://www.noulakaz.net/2007/03/18/a-regular-expression-to-check-for-prime-numbers/
// @see: https://web.archive.org/web/20220522073029/http://montreal.pm.org/tech/neil_kandalgaonkar.shtml#this_vs_mine
// @see: https://web.archive.org/web/20220513113609/http://neilk.net/blog/2000/06/01/abigails-regex-to-test-for-prime-numbers/
// @see: https://web.archive.org/web/20080111215127/http://www.mit.edu:8008/bloom-picayune.mit.edu/perl/10138
// @see: https://news.ycombinator.com/item?id=36413260

function regexp_prime(int $num): bool
{
    return preg_match('/^1?$|^(11+?)\1+$/x', str_repeat('1', $num), $matches) !== 1;
}

foreach ([0, 1, 2, 3, 5, 7, 10, 11, 12, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97, 99, 100] as $num) {
    echo sprintf('%d prime? === %s' . PHP_EOL, $num, (regexp_prime($num) ? 'yes' : 'no'));
}
