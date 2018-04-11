<?php
App::uses('AppModel', 'Model');
class PlainPost extends AppModel {
    public $useTable = 'posts';
    public $hasMany = [
        'PostShareUser'   => [
            'dependent' => true,
        ],
        'PostShareCircle' => [
            'dependent' => true,
        ]
    ];
}
