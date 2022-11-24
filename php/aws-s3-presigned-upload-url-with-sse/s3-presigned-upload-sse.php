<?php
// The X-Amz-Server-Side-Encryption header must be set in formInputs and options, with the correct case.
new PostObjectV4(
    $client,
    $bucket,
    [
        'key' => $key,
        'acl' => 'private',
        'X-Amz-Server-Side-Encryption' => 'AES256',
    ],
    [
        ['acl' => 'private'],
        ['bucket' => $bucket],
        ['key' => $key],
        ['content-length-range', $size, $size],
        ['x-amz-server-side-encryption' => 'AES256'],
    ],
);