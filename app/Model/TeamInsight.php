<?php
App::uses('AppModel', 'Model');

/**
 * TeamInsight Model
 *
 * @property Team $Team
 */
class TeamInsight extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_count'           => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'access_user_count'    => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'message_count'        => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'action_count'         => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'action_user_count'    => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'post_count'           => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'post_user_count'      => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'like_count'           => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'comment_count'        => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'collabo_count'        => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'collabo_action_count' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'              => [
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
    ];
}
