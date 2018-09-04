<?php

$defines = [
    'AWS_S3_BUCKET_USERNAME' => 'travis'
];

if (empty(getenv('DOCKER_ENV'))) {
    array_merge($defines, [
        'REDIS_SESSION_HOST' => 'localhost',
        'REDIS_CACHE_HOST'   => 'localhost',
        'REDIS_HOST'         => 'localhost',
    ]);
}

foreach ($defines as $k => $v) {
    if (!defined($k)) {
        define($k, $v);
    }
}


