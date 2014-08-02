<?php
App::uses('AppModel', 'Model');

/**
 * PostShareUser Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class PostShareUser extends AppModel
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
        'User',
        'Team',
    ];
}
