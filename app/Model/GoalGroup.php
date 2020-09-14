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

    function goalByUserIdSubQuery(int $userId)
    {
        $db = $this->getDataSource();
        return $db->buildStatement([
            "fields" => ['GoalGroup.goal_id'],
            "table" => $db->fullTableName($this),
            "alias" => "GoalGroup",
            "joins" => [$this->joinByUserId($userId)]
        ], $this);
    }

    function joinByUserId(int $userId): array
    {
        return [
            'alias' => 'MemberGroup',
            'table' => 'member_groups',
            'conditions' => [
                'MemberGroup.group_id = GoalGroup.group_id',
                'MemberGroup.user_id' => $userId
            ]
        ];
    }
}
