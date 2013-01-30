#!/usr/bin/env php
<?php
// Left associative ternary.
// Output: T
echo (true ? 'True' : false ? 'T' : 'F') . "\n";

// This is essentially the same as above.
// Output: T
echo ((true ? 'True' : false) ? 'T' : 'F') . "\n";

// Right associative ternary.
// Output: True
echo (true ? 'True' : (false ? 'T' : 'F')) . "\n";