<?php
App::uses('AppModel', 'Model');

/**
 * PostSharedLog Model
 * format of shared_list:
 * {type:add|remove,user:[1,2,3,4],circle:[1,2,3,4]}
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class PostSharedLog extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'shared_list' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'del_flg'     => [
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
