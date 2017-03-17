<?php
// Use a closure so nothing leaks out when included.
return (function () {
    $array = ['foo', 'bar'];

    foreach ($array as $string) {
        // Some exciting logic.
    }
    
    return 'FOOBAR';
})();