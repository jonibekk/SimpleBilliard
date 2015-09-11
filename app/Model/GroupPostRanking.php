<?php
App::uses('AppModel', 'Model');

/**
 * GroupPostRanking Model
 *
 * @property Team  $Team
 * @property Group $Group
 * @property Post  $Post
 */
class GroupPostRanking extends AppModel
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
        'Group',
        'Post',
    ];
}
