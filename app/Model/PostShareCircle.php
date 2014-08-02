<?php
App::uses('AppModel', 'Model');

/**
 * PostShareCircle Model
 *
 * @property Post   $Post
 * @property Circle $Circle
 * @property Team   $Team
 */
class PostShareCircle extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post',
        'Circle',
        'Team',
    ];
}
