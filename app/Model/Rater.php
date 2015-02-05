<?php
App::uses('AppModel', 'Model');

/**
 * Rater Model
 *
 * @property User $RateeUser
 * @property User $RaterUser
 * @property Team $Team
 */
class Rater extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'index'   => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
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
        'RateeUser' => [
            'className'  => 'User',
            'foreignKey' => 'ratee_user_id',
        ],
        'RaterUser' => [
            'className'  => 'User',
            'foreignKey' => 'rater_user_id',
        ],
        'Team',
    ];
}
