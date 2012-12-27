<?php
// http://content.codersdojo.org/code-kata-catalogue/fizz-buzz/
foreach (range(1, 100) as $i) {
    $x  = '';
    $x .= ($i % 3 === 0 ? 'Fizz' : '');
    $x .= ($i % 5 === 0 ? 'Buzz' : '');
    
    echo $i . ' ' . (empty($x) ? $i : $x) . "\n";
}