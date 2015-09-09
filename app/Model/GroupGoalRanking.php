<?php
App::uses('AppModel', 'Model');

/**
 * GroupGoalRanking Model
 *
 * @property Team  $Team
 * @property Group $Group
 * @property Goal  $Goal
 */
class GroupGoalRanking extends AppModel
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
        'Team',
        'Group',
        'Goal',
    ];
}
