<?php
App::uses('AppModel', 'Model');

/**
 * GoalClearEvaluate Model
 *
 * @property Team $Team
 * @property Goal $Goal
 * @property User $User
 */
class GoalClearEvaluate extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'cleared_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
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
        'Team',
        'Goal',
        'User',
    ];
}
