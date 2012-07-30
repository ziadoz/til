<?php
$hash  = BCrypt::hash('password');
$valid = BCrypt::compare('password', $hash);

echo ($valid ? 'Yes' : 'No');