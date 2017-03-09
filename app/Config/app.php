<?php

$config['label'] = [
    'units' => [
        ['id' => 0, 'unit' => '%', 'label' => __('Percentage')],
        ['id' => 3, 'unit' => '¥', 'label' => __('Yen')],
        ['id' => 4, 'unit' => '$', 'label' => __('Dollar')],
        ['id' => 1, 'unit' => '#', 'label' => __('Other numeric')],
        ['id' => 2, 'unit' => '-', 'label' => __('Complete/Incomplete')],
    ],
    'priorities' => [
        ['id' => 1, 'label' => __('1 (Very low)')],
        ['id' => 2, 'label' => __('2')],
        ['id' => 3, 'label' => __('3 (Default)')],
        ['id' => 4, 'label' => __('4')],
        ['id' => 5, 'label' => __('5 (Very high)')],
    ],
];
$config['allow_image_types'] = [
    'image/png',
    'image/gif',
    'image/jpeg',
];

