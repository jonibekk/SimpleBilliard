<?php
App::uses('AppModel', 'Model');

/**
 * GoalGroup Model
 *
 * @property Goal        $Goal
 * @property Group       $Group
 */

class GoalGroup extends AppModel
{
    public $validate = [
        'del_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    public $belongsTo = [
        'Goal',
        'Group',
    ];

    function findGroupsWithGoalId(int $goalId)
    {
        $conditions = [
            'contain' => 'Group',
            'conditions' => [
                'GoalGroup.goal_id' => $goalId
            ]
        ];

        return $this->find('all', $conditions);
    }
}
