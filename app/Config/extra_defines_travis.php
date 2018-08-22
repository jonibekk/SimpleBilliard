<?php

$defines = [
    'AWS_S3_BUCKET_USERNAME' => 'travis',
];

foreach ($defines as $k => $v) {
    if (!defined($k)) {
        define($k, $v);
    }
}


