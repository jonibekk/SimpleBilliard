<?php
App::uses('AppModel', 'Model');

/**
 * TeamPostRanking Model
 *
 * @property Team $Team
 * @property Post $Post
 */
class TeamPostRanking extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'post_type' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'   => [
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
        'Team',
        'Post',
    ];
}
