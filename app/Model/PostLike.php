<?php
App::uses('AppModel', 'Model');

/**
 * PostLike Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class PostLike extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post' => [
            "counterCache" => true,
        ],
        'User',
        'Team',
    ];
}
